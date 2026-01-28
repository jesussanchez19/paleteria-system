@extends('layouts.app')

@section('title', 'POS')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold">POS 🧾</h1>
            <p class="text-slate-600">Selecciona productos para agregar al carrito.</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('dashboard') }}"
               class="px-4 py-2 rounded-xl bg-white border border-slate-200 font-bold text-slate-800 hover:bg-slate-50 transition">
                ← Volver
            </a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="px-4 py-2 rounded-xl bg-slate-900 text-white font-extrabold hover:bg-slate-800 transition">
                    Cerrar sesión
                </button>
            </form>
        </div>
    </div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <section class="lg:col-span-2">
        @if($products->isEmpty())
            <div class="bg-white border border-slate-200 rounded-2xl p-6">
                <p class="font-semibold">No hay productos con stock.</p>
                <p class="text-sm text-slate-600">Agrega stock para vender.</p>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
                @foreach($products as $p)
                    <button
                        type="button"
                        class="text-left bg-white border border-slate-200 rounded-2xl p-4 shadow-sm hover:shadow transition"
                        onclick="addToCart({{ $p->id }}, @js($p->name), {{ (float)$p->price }}, {{ (int)$p->stock }})"
                    >
                        <div class="flex items-start justify-between gap-2">
                            <h2 class="font-bold leading-tight">{{ $p->name }}</h2>
                            <span class="text-xs font-semibold px-2 py-1 rounded-full bg-pink-50 text-pink-700 border border-pink-100">
                                {{ $p->category ?? 'Producto' }}
                            </span>
                        </div>

                        <div class="mt-3 flex items-center justify-between">
                            <p class="text-lg font-extrabold">${{ number_format($p->price, 2) }}</p>
                            <p class="text-sm text-slate-600">Stock: <b>{{ $p->stock }}</b></p>
                        </div>

                        <p class="mt-2 text-xs text-slate-500">Click para agregar</p>
                    </button>
                @endforeach
            </div>
        @endif
    </section>

    <aside class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-extrabold">Carrito</h2>
            <button type="button" class="text-sm font-semibold text-slate-600 hover:underline" onclick="clearCart()">
                Vaciar
            </button>
        </div>

        <div id="cart-items" class="mt-4 space-y-3">
            <p class="text-sm text-slate-600" id="cart-empty">Aún no agregas productos.</p>
        </div>

        <div class="mt-6 border-t border-slate-200 pt-4">
            <div class="space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="text-slate-600">Subtotal</span>
                    <span id="subtotal" class="font-semibold text-slate-900">$0.00</span>
                </div>
                <div class="flex justify-between text-2xl font-extrabold bg-gradient-to-r from-emerald-50 to-teal-50 rounded-lg p-3">
                    <span class="text-slate-900">Total</span>
                    <span id="total" class="text-emerald-600">$0.00</span>
                </div>
            </div>

            <button
                type="button"
                class="mt-5 w-full bg-yellow-300 hover:bg-yellow-400 active:bg-yellow-500 text-slate-900 font-extrabold py-4 px-4 rounded-xl shadow-md hover:shadow-lg transition disabled:bg-yellow-200 disabled:cursor-not-allowed disabled:shadow-md text-lg"
                style="background-color: #fcd34d; color: #1f2937; padding: 1rem 1rem; font-weight: 700; border-radius: 0.75rem; width: 100%; border: none; cursor: pointer; font-size: 1.125rem;"
                id="btn-cobrar"
                onclick="checkout()"
                disabled
            >
                Cobrar
            </button>
        </div>
    </aside>
</div>

</div>

<script>
    const cart = new Map();

    function money(n) {
        const v = Math.round(n * 100) / 100;
        return '$' + v.toFixed(2);
    }

    function addToCart(id, name, price, stock) {
        const item = cart.get(id) || { id, name, price, stock, qty: 0 };

        if (item.qty + 1 > stock) {
            alert('Stock insuficiente para "' + name + '".');
            return;
        }

        item.qty += 1;
        cart.set(id, item);
        renderCart();
    }

    function changeQty(id, newQty) {
        const item = cart.get(id);
        if (!item) return;

        if (newQty === '' || newQty === null || newQty === undefined) return;

        let qty = parseInt(newQty, 10);
        if (Number.isNaN(qty) || qty < 1) qty = 1;

        if (qty > item.stock) {
            alert('Stock insuficiente para "' + item.name + '".');
            qty = item.stock;
        }

        item.qty = qty;
        cart.set(id, item);

        // Actualizar sólo el renglón y totales para evitar re-render completo
        const input = document.querySelector(`input[data-id="${id}"]`);
        if (input) input.value = item.qty;

        const subtotalEl = document.querySelector(`[data-subtotal="${id}"]`);
        if (subtotalEl) subtotalEl.innerText = money(item.qty * item.price);

        const t = totals();
        document.getElementById('subtotal').innerText = money(t.subtotal);
        document.getElementById('total').innerText = money(t.total);

        document.getElementById('btn-cobrar').disabled = cart.size === 0;
    }

    function incrementQty(id) {
        const item = cart.get(id);
        if (!item) return;
        changeQty(id, item.qty + 1);
    }

    function decrementQty(id) {
        const item = cart.get(id);
        if (!item) return;
        changeQty(id, item.qty - 1);
    }

    function removeItem(id) {
        cart.delete(id);
        renderCart();
    }

    function clearCart() {
        cart.clear();
        renderCart();
    }

    function totals() {
        let subtotal = 0;
        for (const item of cart.values()) {
            subtotal += item.qty * item.price;
        }
        return { subtotal, total: subtotal };
    }

    function renderCart() {
        const container = document.getElementById('cart-items');
        container.innerHTML = '';

        if (cart.size === 0) {
            // Recrear el elemento vacío ya que lo borramos con innerHTML = ''
            const empty = document.createElement('p');
            empty.id = 'cart-empty';
            empty.className = 'text-sm text-slate-600';
            empty.textContent = 'Aún no agregas productos.';
            container.appendChild(empty);
            document.getElementById('btn-cobrar').disabled = true;
            document.getElementById('btn-cobrar').style.backgroundColor = '#fef3c7'; // amarillo claro (disabled)
        } else {
            for (const item of cart.values()) {
                const row = document.createElement('div');
                row.className = 'border border-slate-200 rounded-xl p-3';

                row.innerHTML = `
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <p class="font-bold">${item.name}</p>
                            <p class="text-xs text-slate-600">Precio: ${money(item.price)} • Stock: ${item.stock}</p>
                        </div>
                        <button type="button" class="text-xs font-semibold text-rose-600 hover:underline" onclick="removeItem(${item.id})">
                            Quitar
                        </button>
                    </div>

                    <div class="mt-2 flex items-center justify-between">
                        <label class="text-xs text-slate-600">Cantidad</label>
                        <div class="flex items-center gap-2">
                            <button type="button" onclick="decrementQty(${item.id})" class="px-2 py-1 bg-slate-100 rounded">-</button>
                            <input
                                data-qty-input="1"
                                data-id="${item.id}"
                                type="number" min="1" max="${item.stock}" value="${item.qty}"
                                class="w-20 border border-slate-200 rounded-lg px-2 py-1 text-sm text-center"
                            >
                            <button type="button" onclick="incrementQty(${item.id})" class="px-2 py-1 bg-slate-100 rounded">+</button>
                        </div>
                    </div>

                    <div class="mt-2 flex justify-between text-sm">
                        <span class="text-slate-600">Subtotal</span>
                        <span class="font-bold" data-subtotal="${item.id}">${money(item.qty * item.price)}</span>
                    </div>
                `;

                container.appendChild(row);
            }

            document.getElementById('btn-cobrar').disabled = false;
            document.getElementById('btn-cobrar').style.backgroundColor = '#fcd34d'; // amarillo activo
        }

        const t = totals();
        document.getElementById('subtotal').innerText = money(t.subtotal);
        document.getElementById('total').innerText = money(t.total);
    }

    // Delegación de eventos para inputs de cantidad (Enter)
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' && e.target && e.target.matches('input[data-qty-input="1"]')) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            const id = parseInt(e.target.getAttribute('data-id'), 10);
            changeQty(id, e.target.value);
            e.target.blur();
        }
    }, true);

    function checkout() {
        if (cart.size === 0) {
            alert('El carrito está vacío');
            return;
        }

        // Primero, mostrar mensaje de entrega
        alert('Entrega la mercancía y recibe el dinero');

        // Solicitar la cantidad de dinero recibida
        let receivedAmount = null;
        let totalAmount = null;
        let validInput = false;

        // Obtener el total antes de enviar la venta
        const t = totals();
        totalAmount = t.total;

        while (!validInput) {
            const input = prompt(`Total a pagar: $${totalAmount}\n\nIngresa la cantidad de dinero que recibiste:`);
            if (input === null) {
                // Cancelado
                return;
            }
            receivedAmount = parseFloat(input);
            if (isNaN(receivedAmount)) {
                alert('Cantidad inválida');
            } else if (receivedAmount < totalAmount) {
                alert(`Falta dinero. Falta: $${(totalAmount - receivedAmount).toFixed(2)}`);
            } else {
                validInput = true;
            }
        }

        // Mensaje de cambio o pago exacto
        if (receivedAmount === totalAmount) {
            alert('Pago exacto');
        } else {
            const change = (receivedAmount - totalAmount).toFixed(2);
            alert(`Cambio a dar: $${change}`);
        }

        // Ahora sí, registrar la venta
        const items = [];
        for (const item of cart.values()) {
            items.push({ id: item.id, qty: item.qty });
        }

        fetch("{{ route('pos.checkout') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ items })
        })
        .then(res => {
            if (!res.ok) {
                return res.json().then(err => {
                    throw new Error(err.message || 'Error al registrar la venta');
                });
            }
            return res.json();
        })
        .then(data => {
            alert(`Venta realizada. Total: $${data.total}`);
            window.location.reload();
        })
        .catch(err => {
            console.error('Error:', err);
            alert('Error: ' + err.message);
        });
    }
</script>
@endsection
