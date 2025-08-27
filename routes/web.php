<?php

use App\Http\Controllers\Settings\ProfileController;
use App\Http\Controllers\PdfParseController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\AiModelController;
use App\Http\Controllers\ApiKeyController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\DashboardController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->name('home');

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

// PDF Parse Routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/pdf-parse', [PdfParseController::class, 'index'])->name('pdf-parse.index');
    Route::get('/pdf-parse/history', [PdfParseController::class, 'history'])->name('pdf-parse.history');
    Route::post('/pdf-parse', [PdfParseController::class, 'store'])->name('pdf-parse.store');
    Route::get('/pdf-parse/{pdfParse}', [PdfParseController::class, 'show'])->name('pdf-parse.show');
    Route::delete('/pdf-parse/{pdfParse}', [PdfParseController::class, 'destroy'])->name('pdf-parse.destroy');
});

// API Keys Management
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/api-keys', [ApiKeyController::class, 'index'])->name('api-keys.index');
    Route::post('/api-keys', [ApiKeyController::class, 'store'])->name('api-keys.store');
    Route::patch('/api-keys/{apiKey}/toggle', [ApiKeyController::class, 'toggle'])->name('api-keys.toggle');
    Route::delete('/api-keys/{apiKey}', [ApiKeyController::class, 'destroy'])->name('api-keys.destroy');
});

// User Subscriptions
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/subscriptions', [SubscriptionController::class, 'index'])->name('subscriptions.index');
    Route::get('/subscriptions/{subscription}', [SubscriptionController::class, 'show'])->name('subscriptions.show');
    
    // Payment routes
    Route::get('/subscriptions/{subscription}/payment', [PaymentController::class, 'showPaymentForm'])->name('subscriptions.payment');
    Route::post('/subscriptions/{subscription}/create-order', [PaymentController::class, 'createOrder'])->name('subscriptions.create-order');
    Route::get('/payment/success', [PaymentController::class, 'paymentSuccess'])->name('payment.success');
    Route::get('/payment/failure', [PaymentController::class, 'paymentFailure'])->name('payment.failure');
    Route::get('/payment/status', [PaymentController::class, 'checkStatus'])->name('payment.status');
});

// Webhook routes (no auth required)
Route::post('/webhooks/razorpay', [PaymentController::class, 'webhook'])->name('webhooks.razorpay');

// Admin Routes (Super Users Only)
Route::middleware(['auth', 'verified', 'role:super'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
    Route::get('/users/{user}', [UserManagementController::class, 'show'])->name('users.show');
    Route::patch('/users/{user}/role', [UserManagementController::class, 'updateRole'])->name('users.update-role');
    Route::post('/users/{user}/subscription', [UserManagementController::class, 'assignSubscription'])->name('users.assign-subscription');
    Route::delete('/users/{user}/subscription', [UserManagementController::class, 'revokeSubscription'])->name('users.revoke-subscription');
    
    // AI Models - Custom routes first, then resource
    Route::patch('/ai-models/{ai_model}/toggle', [AiModelController::class, 'toggleStatus'])->name('ai-models.toggle');
    Route::resource('ai-models', AiModelController::class);
    
    // Admin Subscriptions
    Route::get('/subscriptions', [SubscriptionController::class, 'adminIndex'])->name('subscriptions.index');
    Route::get('/subscriptions/{subscription}', [SubscriptionController::class, 'adminShow'])->name('subscriptions.show');
    
    // Analytics
    Route::get('/analytics', function () {
        return Inertia::render('Admin/Analytics/Index');
    })->name('analytics.index');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Documentation and Support
Route::get('/api-documentation', function () {
    return redirect('/api-documentation.md');
})->name('api-documentation');

Route::get('/support', function () {
    return Inertia::render('Support/Index');
})->name('support');

require __DIR__.'/auth.php';
require __DIR__.'/settings.php';
