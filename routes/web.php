<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\PortalController;
use App\Http\Controllers\SuperAdmin\DashboardController as SuperAdminDashboard;
use App\Http\Controllers\SuperAdmin\TenantController;
use App\Http\Controllers\Tenant\AgentController;
use App\Http\Controllers\Tenant\BookingController;
use App\Http\Controllers\Tenant\CommissionController;
use App\Http\Controllers\Tenant\DashboardController;
use App\Http\Controllers\Tenant\DocumentController;
use App\Http\Controllers\Tenant\DocumentTypeController;
use App\Http\Controllers\Tenant\InquiryController;
use App\Http\Controllers\Tenant\InvoiceController;
use App\Http\Controllers\Tenant\JamaahController;
use App\Http\Controllers\Tenant\ManasikController;
use App\Http\Controllers\Tenant\PackageController;
use App\Http\Controllers\Tenant\PaymentController;
use App\Http\Controllers\Tenant\ReportController;
use App\Http\Controllers\Tenant\RombonganController;
use App\Http\Controllers\Tenant\SettingController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route(Auth::user()->isSuperAdmin() ? 'admin.dashboard' : 'app.dashboard');
    }

    return view('welcome');
})->name('home');

// Authentication
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
});
Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')->name('logout');

// Super Admin (platform)
Route::middleware(['auth', 'superadmin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [SuperAdminDashboard::class, 'index'])->name('dashboard');
    Route::get('tenants', [TenantController::class, 'index'])->name('tenants.index');
    Route::get('tenants/create', [TenantController::class, 'create'])->name('tenants.create');
    Route::post('tenants', [TenantController::class, 'store'])->name('tenants.store');
    Route::get('tenants/{tenant}', [TenantController::class, 'show'])->name('tenants.show');
    Route::get('tenants/{tenant}/edit', [TenantController::class, 'edit'])->name('tenants.edit');
    Route::put('tenants/{tenant}', [TenantController::class, 'update'])->name('tenants.update');
    Route::post('tenants/{tenant}/toggle', [TenantController::class, 'toggle'])->name('tenants.toggle');
    Route::post('tenants/{tenant}/reset-password', [TenantController::class, 'resetOwnerPassword'])->name('tenants.reset-password');
});

// Tenant application
Route::middleware(['auth', 'tenant'])->prefix('app')->name('app.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('packages', PackageController::class)->except(['show']);
    Route::get('packages/{package}', [PackageController::class, 'show'])->name('packages.show');

    Route::resource('jamaah', JamaahController::class)->parameters(['jamaah' => 'jamaah']);

    Route::resource('bookings', BookingController::class)->only(['index', 'create', 'store', 'show', 'destroy']);

    Route::get('invoices', [InvoiceController::class, 'index'])->name('invoices.index');
    Route::get('invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');
    Route::get('invoices/{invoice}/pdf', [InvoiceController::class, 'pdf'])->name('invoices.pdf');

    Route::get('payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::get('payments/cashier', [PaymentController::class, 'cashier'])->name('payments.cashier');
    Route::post('payments/cashier', [PaymentController::class, 'cashierStore'])->name('payments.cashier.store');
    Route::get('payments/cashier/{payment}/success', [PaymentController::class, 'cashierSuccess'])->name('payments.cashier.success');
    Route::get('payments/create', [PaymentController::class, 'create'])->name('payments.create');
    Route::post('payments', [PaymentController::class, 'store'])->name('payments.store');
    Route::post('payments/{payment}/verify', [PaymentController::class, 'verify'])->name('payments.verify');
    Route::post('payments/{payment}/reject', [PaymentController::class, 'reject'])->name('payments.reject');
    Route::get('payments/{payment}/receipt', [PaymentController::class, 'receipt'])->name('payments.receipt');

    Route::get('documents', [DocumentController::class, 'index'])->name('documents.index');
    Route::post('jamaah/{jamaah}/documents/sync', [DocumentController::class, 'sync'])->name('documents.sync');
    Route::post('documents/{document}/upload', [DocumentController::class, 'upload'])->name('documents.upload');
    Route::post('documents/{document}/verify', [DocumentController::class, 'verify'])->name('documents.verify');
    Route::post('documents/{document}/reject', [DocumentController::class, 'reject'])->name('documents.reject');

    Route::get('document-types', [DocumentTypeController::class, 'index'])->name('document-types.index');
    Route::post('document-types', [DocumentTypeController::class, 'store'])->name('document-types.store');
    Route::put('document-types/{documentType}', [DocumentTypeController::class, 'update'])->name('document-types.update');
    Route::delete('document-types/{documentType}', [DocumentTypeController::class, 'destroy'])->name('document-types.destroy');

    Route::resource('rombongan', RombonganController::class)->parameters(['rombongan' => 'rombongan']);
    Route::post('rombongan/{rombongan}/assign', [RombonganController::class, 'assign'])->name('rombongan.assign');
    Route::post('rombongan-member/{member}/unassign', [RombonganController::class, 'unassign'])->name('rombongan.unassign');

    Route::resource('manasik', ManasikController::class)->parameters(['manasik' => 'manasik']);
    Route::post('manasik/{manasik}/attendance', [ManasikController::class, 'attendance'])->name('manasik.attendance');

    Route::resource('agents', AgentController::class);
    Route::get('commissions', [CommissionController::class, 'index'])->name('commissions.index');
    Route::post('commissions/{commission}/pay', [CommissionController::class, 'pay'])->name('commissions.pay');

    Route::get('inquiries', [InquiryController::class, 'index'])->name('inquiries.index');
    Route::post('inquiries/{inquiry}/convert', [InquiryController::class, 'convert'])->name('inquiries.convert');
    Route::post('inquiries/{inquiry}/status', [InquiryController::class, 'updateStatus'])->name('inquiries.status');

    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/jamaah/export', [ReportController::class, 'exportJamaah'])->name('reports.jamaah');
    Route::get('reports/payments/export', [ReportController::class, 'exportPayments'])->name('reports.payments');

    Route::get('settings', [SettingController::class, 'edit'])->name('settings.edit');
    Route::put('settings', [SettingController::class, 'update'])->name('settings.update');
});

// Public portal per tenant
Route::middleware('public.tenant')->prefix('t/{tenantSlug}')->name('portal.')->group(function () {
    Route::get('/', [PortalController::class, 'index'])->name('index');
    Route::get('paket', [PortalController::class, 'packages'])->name('packages');
    Route::get('paket/{package}', [PortalController::class, 'package'])->name('package');
    Route::post('inquiry', [PortalController::class, 'storeInquiry'])->name('inquiry');
});
