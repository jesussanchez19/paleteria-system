<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CashRegister;
use App\Models\Sale;

class CashRegisterController extends Controller
{
    /**
     * Mostrar estado de caja actual
     */
    public function status()
    {
        // Sincronizar caja con horario laboral (abre/cierra automáticamente)
        $openRegister = CashRegister::syncWithBusinessHours();
        
        $salesDuringShift = 0;
        $expectedAmount = 0;
        
        if ($openRegister) {
            $salesDuringShift = Sale::where('created_at', '>=', $openRegister->opened_at)->sum('total');
            $expectedAmount = (float)$openRegister->opening_amount + $salesDuringShift;
        }
        
        $businessHours = CashRegister::getBusinessHoursInfo();
        
        return response()->json([
            'open' => $openRegister !== null,
            'register' => $openRegister,
            'sales_during_shift' => $salesDuringShift,
            'expected_amount' => $expectedAmount,
            'business_hours' => $businessHours,
        ]);
    }

    /**
     * Abrir caja
     */
    public function open(Request $request)
    {
        $request->validate([
            'opening_amount' => 'required|numeric|min:0',
        ]);

        // Verificar si ya hay una caja abierta
        $existingOpen = CashRegister::getOpenRegister();
        if ($existingOpen) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ya hay una caja abierta. Ciérrala primero.',
                ], 422);
            }
            return back()->with('error', 'Ya hay una caja abierta. Ciérrala primero.');
        }

        $cash = CashRegister::create([
            'user_id' => auth()->id(),
            'opening_amount' => $request->opening_amount,
            'opened_at' => now(),
        ]);

        audit_log('cash.opened', 'caja', $cash, [
            'monto_apertura' => '$' . number_format($request->opening_amount, 2),
            'usuario' => auth()->user()->name,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Caja abierta correctamente.',
            ]);
        }

        return back()->with('success', 'Caja abierta correctamente.');
    }

    /**
     * Cerrar caja (sin monto real, se ingresa después en panel/caja)
     */
    public function close(Request $request)
    {
        $cash = CashRegister::getOpenRegister();

        if (!$cash) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay caja abierta.',
                ], 422);
            }
            return back()->with('error', 'No hay caja abierta.');
        }

        // Calcular ventas del turno (desde que se abrió la caja)
        $salesDuringShift = Sale::where('created_at', '>=', $cash->opened_at)->sum('total');
        
        // Expected = apertura + ventas del turno
        $expected = (float)$cash->opening_amount + $salesDuringShift;

        $cash->update([
            'expected_amount' => $expected,
            'closed_at' => now(),
        ]);

        audit_log('cash.closed', 'caja', $cash, [
            'esperado' => '$' . number_format($expected, 2),
            'nota' => 'Pendiente registrar dinero real',
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Caja cerrada. Registra el dinero real en la sección de Caja.',
            ]);
        }

        return redirect()->route('panel.caja.index')
            ->with('success', 'Caja cerrada. Ahora registra el dinero real contado.');
    }

    /**
     * Registrar dinero real después del cierre automático
     */
    public function registerRealAmount(Request $request)
    {
        $request->validate([
            'cash_register_id' => 'required|exists:cash_registers,id',
            'closing_amount' => 'required|numeric|min:0',
        ]);

        $cash = CashRegister::findOrFail($request->cash_register_id);

        // Verificar que la caja esté cerrada pero sin dinero real registrado
        if (!$cash->closed_at) {
            return back()->with('error', 'La caja aún no ha sido cerrada.');
        }

        if ($cash->closing_amount !== null) {
            return back()->with('error', 'El dinero real ya fue registrado para esta caja.');
        }

        // Calcular diferencia
        $difference = $request->closing_amount - (float)$cash->expected_amount;

        $cash->update([
            'closing_amount' => $request->closing_amount,
            'difference' => $difference,
        ]);

        audit_log('cash.amount_registered', 'caja', $cash, [
            'esperado' => '$' . number_format($cash->expected_amount, 2),
            'dinero_real' => '$' . number_format($request->closing_amount, 2),
            'diferencia' => '$' . number_format($difference, 2),
            'registrado_por' => auth()->user()->name,
        ]);

        $msg = 'Dinero real registrado. Diferencia: $' . number_format($difference, 2);
        
        return back()->with('success', $msg);
    }
}
