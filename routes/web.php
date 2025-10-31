<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\SignatureController;

Route::get('/', function () {
    return redirect()->route('documents.index');
});

Route::resource('documents', DocumentController::class);
Route::get('documents/{id}/download', [DocumentController::class, 'download'])->name('documents.download');
Route::get('documents/{id}/qrcode', [DocumentController::class, 'qrcode'])->name('documents.qrcode');
Route::get('signatures', [DocumentController::class, 'signatures'])->name('documents.signatures');

Route::get('sign/{token}', [SignatureController::class, 'show'])->name('signatures.show');
Route::post('sign/{token}', [SignatureController::class, 'store'])->name('signatures.store');
Route::get('sign/{token}/success', [SignatureController::class, 'success'])->name('signatures.success');
Route::get('verify/{token}', [SignatureController::class, 'verify'])->name('signatures.verify');
