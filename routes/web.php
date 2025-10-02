<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Shop\ProductController;
use App\Http\Controllers\Shop\CartController;
use App\Http\Controllers\Shop\CheckoutController;
use App\Http\Controllers\Shop\OrdersController;

// Home Routes
Route::get('/', [HomeController::class, 'index'])->name('home');

// Shop Routes
Route::prefix('shop')->name('shop.')->group(function () {
    // Product Routes
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');
    Route::post('/products/{product}/add-to-cart', [ProductController::class, 'addToCart'])->name('products.add-to-cart');

    // Cart Routes
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add/{product}', [CartController::class, 'addProduct'])->name('cart.add');
    Route::post('/cart/update-quantity', [CartController::class, 'updateQuantity'])->name('cart.update-quantity');
    Route::post('/cart/remove', [CartController::class, 'removeProduct'])->name('cart.remove');
    Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');
    Route::get('/cart/data', [CartController::class, 'getCartData'])->name('cart.data');
    Route::get('/cart/widget', [CartController::class, 'getCartWidget'])->name('cart.widget');
    Route::post('/cart/validate', [CartController::class, 'validate'])->name('cart.validate');
    Route::post('/cart/proceed-to-checkout', [CartController::class, 'proceedToCheckout'])->name('cart.proceed-to-checkout');
    Route::post('/cart/apply-promo', [CartController::class, 'applyPromoCode'])->name('cart.apply-promo');

    // Checkout Routes
    Route::prefix('checkout')->name('checkout.')->group(function () {
        Route::get('/', [CheckoutController::class, 'index'])->name('index');
        Route::get('/{step}', [CheckoutController::class, 'showStep'])->name('step')
            ->where('step', 'customer|payment|review|complete');
        Route::post('/process/customer', [CheckoutController::class, 'processCustomer'])->name('process.customer');
        Route::post('/process/payment', [CheckoutController::class, 'processPayment'])->name('process.payment');
        Route::post('/process/order', [CheckoutController::class, 'processOrder'])->name('process.order');
        Route::get('/callback/{order}', [CheckoutController::class, 'callback'])->name('callback');
    });

});

Route::prefix('sdk')->name('sdk.')->group(function () {
    Route::get('/showcase', [App\Http\Controllers\SDK\ShowcaseController::class, 'index'])->name('showcase');

    // Products API Routes
    Route::get('/products', [App\Http\Controllers\SDK\ProductsController::class, 'index'])->name('products');
    Route::get('/products/test', [App\Http\Controllers\SDK\ProductsController::class, 'test']); // Test endpoint
    Route::get('/products/test-delete', [App\Http\Controllers\SDK\ProductsController::class, 'testDelete']); // Test delete
    Route::post('/products/create', [App\Http\Controllers\SDK\ProductsController::class, 'createProduct']);
    Route::post('/products/get', [App\Http\Controllers\SDK\ProductsController::class, 'getProducts']);
    Route::post('/products/get-single', [App\Http\Controllers\SDK\ProductsController::class, 'getProduct']);
    Route::post('/products/update', [App\Http\Controllers\SDK\ProductsController::class, 'updateProduct']);
    Route::post('/products/delete', [App\Http\Controllers\SDK\ProductsController::class, 'deleteProduct']);

    // Orders API Routes
    Route::get('/orders', [App\Http\Controllers\SDK\OrdersController::class, 'index'])->name('orders');
    Route::post('/orders/create', [App\Http\Controllers\SDK\OrdersController::class, 'createOrder']);
    Route::post('/orders/get', [App\Http\Controllers\SDK\OrdersController::class, 'getOrders']);
    Route::post('/orders/get-single', [App\Http\Controllers\SDK\OrdersController::class, 'getOrder']);
    Route::post('/orders/capture', [App\Http\Controllers\SDK\OrdersController::class, 'capturePayment']);
    Route::post('/orders/refund', [App\Http\Controllers\SDK\OrdersController::class, 'refundPayment']);
    Route::post('/orders/cancel', [App\Http\Controllers\SDK\OrdersController::class, 'cancelOrder']);
    // Clients API Routes
    Route::get('/clients', [App\Http\Controllers\SDK\ClientsController::class, 'index'])->name('clients');
    Route::post('/clients/create', [App\Http\Controllers\SDK\ClientsController::class, 'createClient']);
    Route::post('/clients/get', [App\Http\Controllers\SDK\ClientsController::class, 'getClients']);
    Route::post('/clients/get-single', [App\Http\Controllers\SDK\ClientsController::class, 'getClient']);
    Route::post('/clients/update', [App\Http\Controllers\SDK\ClientsController::class, 'updateClient']);
    Route::post('/clients/partial-update', [App\Http\Controllers\SDK\ClientsController::class, 'partialUpdateClient']);
    Route::post('/clients/delete', [App\Http\Controllers\SDK\ClientsController::class, 'deleteClient']);

    // Webhooks API Routes
    Route::get('/webhooks', [App\Http\Controllers\SDK\WebhooksController::class, 'index'])->name('webhooks');
    Route::post('/webhooks/create', [App\Http\Controllers\SDK\WebhooksController::class, 'createWebhook']);
    Route::post('/webhooks/get', [App\Http\Controllers\SDK\WebhooksController::class, 'getWebhooks']);
    Route::post('/webhooks/get-single', [App\Http\Controllers\SDK\WebhooksController::class, 'getWebhook']);
    Route::post('/webhooks/update', [App\Http\Controllers\SDK\WebhooksController::class, 'updateWebhook']);
    Route::post('/webhooks/delete', [App\Http\Controllers\SDK\WebhooksController::class, 'deleteWebhook']);
});

Route::prefix('payment')->name('payment.')->group(function () {
    Route::get('/cards', function () { return view('payment.cards'); })->name('cards');
    Route::get('/digital-wallets', function () { return view('coming-soon', ['title' => 'Digital Wallets']); })->name('digital-wallets');
    Route::get('/alternative', function () { return view('coming-soon', ['title' => 'Alternative Payment Methods']); })->name('alternative');
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Admin\AdminController::class, 'dashboard'])->name('dashboard');

    // Orders Management
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\OrdersController::class, 'index'])->name('index');
        Route::get('/{order}', [App\Http\Controllers\Admin\OrdersController::class, 'show'])->name('show');
        Route::post('/{order}/capture', [App\Http\Controllers\Admin\OrdersController::class, 'capturePayment'])->name('capture');
        Route::post('/{order}/refund', [App\Http\Controllers\Admin\OrdersController::class, 'refund'])->name('refund');
        Route::post('/{order}/cancel', [App\Http\Controllers\Admin\OrdersController::class, 'cancel'])->name('cancel');
        Route::get('/{order}/sync', [App\Http\Controllers\Admin\OrdersController::class, 'syncStatus'])->name('sync');
    });

    // Customers Management
    Route::prefix('customers')->name('customers.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\CustomersController::class, 'index'])->name('index');
        Route::get('/{customer}', [App\Http\Controllers\Admin\CustomersController::class, 'show'])->name('show');
        Route::post('/{customer}/sync', [App\Http\Controllers\Admin\CustomersController::class, 'syncWithGateway'])->name('sync');
    });

    // Products Management
    Route::prefix('products')->name('products.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\ProductsController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Admin\ProductsController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Admin\ProductsController::class, 'store'])->name('store');
        Route::get('/{product}', [App\Http\Controllers\Admin\ProductsController::class, 'show'])->name('show');
        Route::get('/{product}/edit', [App\Http\Controllers\Admin\ProductsController::class, 'edit'])->name('edit');
        Route::put('/{product}', [App\Http\Controllers\Admin\ProductsController::class, 'update'])->name('update');
        Route::delete('/{product}', [App\Http\Controllers\Admin\ProductsController::class, 'destroy'])->name('destroy');
        Route::post('/{product}/sync', [App\Http\Controllers\Admin\ProductsController::class, 'syncWithGateway'])->name('sync');
    });

    // Payments Management
    Route::prefix('payments')->name('payments.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\PaymentsController::class, 'index'])->name('index');
        Route::get('/{payment}', [App\Http\Controllers\Admin\PaymentsController::class, 'show'])->name('show');
    });

    // Webhooks Management
    Route::prefix('webhooks')->name('webhooks.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\WebhooksController::class, 'index'])->name('index');
        Route::get('/{webhookLog}', [App\Http\Controllers\Admin\WebhooksController::class, 'show'])->name('show');
        Route::post('/{webhookLog}/reprocess', [App\Http\Controllers\Admin\WebhooksController::class, 'reprocess'])->name('reprocess');
    });
});

// Webhook Routes
Route::prefix('webhook')->name('webhook.')->group(function () {
    Route::post('/payment', function () {
        // Demo webhook endpoint
        return response()->json(['status' => 'received']);
    })->name('payment');
});
