<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\AdminContactController;

Route::get('/', [ContactController::class, 'create'])->name('contact.create');
Route::get('/confirm', function () {
    return redirect()->route('contact.create');
});
Route::post('/confirm', [ContactController::class, 'confirm'])->name('contact.confirm');
Route::post('/thanks', [ContactController::class, 'store'])->name('contact.store');
Route::get('/thanks', [ContactController::class, 'thanks'])->name('contact.thanks');

Route::middleware('auth')->get('/reset', function () {
    return redirect()->route('admin.index');
})->name('reset');

Route::middleware('auth')->prefix('admin')->group(function () {
    Route::get('/', [AdminContactController::class, 'index'])->name('admin.index');
    Route::get('/search', [AdminContactController::class, 'search'])->name('admin.search');
    Route::post('/delete', [AdminContactController::class, 'destroy'])->name('admin.delete');
    Route::get('/export', [AdminContactController::class, 'export'])->name('admin.export');
    Route::get('/{contact}', [AdminContactController::class, 'show'])->name('admin.show');
});
