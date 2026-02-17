<?php

use App\Models\Product;

test('it correctly identifies low stock', function () {
    $product = new Product(['stock' => 5]);
    expect($product->isLowStock(10))->toBeTrue();
    expect($product->isLowStock(5))->toBeTrue();
    expect($product->isLowStock(4))->toBeTrue();
});

test('it correctly identifies sufficient stock', function () {
    $product = new Product(['stock' => 11]);
    expect($product->isLowStock(10))->toBeFalse();
    expect($product->isLowStock(5))->toBeFalse();
});
