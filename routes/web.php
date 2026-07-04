<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\CustomerForm;
use App\Http\Controllers\CustomerPdfController;
use App\Livewire\PublicAppointment;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/lang/{locale}', function ($locale) {

    if (in_array($locale, ['es', 'en'])) {
        Session::put('locale', $locale);
    }

    return back();

})->name('lang.switch');

Route::get('/form-customer', function () {
    return view('formCustomer');
})->name('customer.formCustomer');

Route::get('/customer/pdf/{customer}', [CustomerPdfController::class, 'generate'])
    ->name('customer.pdf');

Route::get(
    '/agendamiento',
    PublicAppointment::class
);