<?php

use App\Http\Controllers\Auth\GoogleController;
use App\Livewire\MailerManager;
use App\Livewire\MailerItemManager;
use App\Livewire\CreateMailer;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');
    
    // Mailer Management Routes
    Route::get('mailers', MailerManager::class)->name('mailers.index');
    Route::get('mailers/create', CreateMailer::class)->name('mailers.create');
    Route::get('mailers/{mailerId}/recipients', MailerItemManager::class)->name('mailers.recipients');
});

// Google OAuth Routes
Route::get('/auth/google', [GoogleController::class, 'redirect'])->name('google.redirect');
Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('google.callback');

require __DIR__.'/auth.php';
