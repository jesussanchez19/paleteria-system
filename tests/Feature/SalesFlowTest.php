<?php

use App\Models\Product;
use App\Models\User;
use App\Models\Sale;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('full sales flow: user can checkout and stock is deducted', function () {
    // 1. Setup Data
    $user = User::factory()->create(['role' => 'vendedor']);
    $product = Product::factory()->create([
        'stock' => 10,
        'price' => 50,
        'is_active' => true
    ]);

    // 2. Simulate Auth
    $this->actingAs($user);

    // 3. Perform Action (Checkout)
    $response = $this->post(route('pos.checkout'), [
        'items' => [
            [
                'product_id' => $product->id,
                'qty' => 2,
            ]
        ],
        'total' => 100,
    ]);

    // 4. Assertions

    // Check Redirect or Success Response
    $response->assertSessionHasNoErrors();
    $response->assertRedirect();

    // Check Database (Sale Created)
    $this->assertDatabaseHas('sales', [
        'total' => 100
    ]);

    // Check Database (Sale Detail Created)
    $sale = Sale::latest()->first();
    $this->assertDatabaseHas('sale_details', [
        'sale_id' => $sale->id,
        'product_id' => $product->id,
        'qty' => 2,
        'price_unit' => $product->price
    ]);

    // Critical Business Logic: Stock Deducted
    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'stock' => 8 // 10 - 2 = 8
    ]);
});
