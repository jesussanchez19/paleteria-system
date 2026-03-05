<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;

class AuditLogController extends Controller
{
    public function index()
    {
        $logs = AuditLog::with('user')
            ->orderByDesc('id')
            ->paginate(50);

        return view('panel.bitacora', compact('logs'));
    }
}
