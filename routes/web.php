<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ContactController;

// Guest Middleware: Only Allow Unauthenticated Users to Access Login & Register
Route::middleware(['guest'])->group(function () {
    Route::get('/register', [RegisterController::class, 'showForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
    Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/', [LoginController::class, 'login'])->name('login.submit');
    Route::post('/reset-password', [LoginController::class, 'resetPassword'])->name('reset.password');
});

// Auth Middleware: Protect These Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('contacts.index');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/search-contacts', [ContactController::class, 'search'])->name('contacts.search');
    Route::get('/favorites/search', [ContactController::class, 'searchFavorites']);
    Route::get('/profile', [HomeController::class, 'profile'])->name('profile');
    Route::post('/profile/upload-avatar', [HomeController::class, 'uploadAvatar'])->name('profile.uploadAvatar');
    Route::put('/profile/update',[HomeController::class,'update'])->name('profile.update');
    Route::delete('/profile/remove-avatar', [HomeController::class, 'removeAvatar'])->name('profile.removeAvatar');

    // Contacts Routes
    Route::get('/contacts', [HomeController::class, 'contact'])->name('contacts.contact');
    Route::delete('contacts/{id}', [HomeController::class, 'destroy'])->name('contacts.destroy');
    
    // Add & Edit Contacts
    Route::get('addcontact', [ContactController::class, 'create'])->name('addcontact.create');
    Route::post('addcontact', [ContactController::class, 'addContact'])->name('addcontact.store');
    Route::get('/edit-contact/{id}', [ContactController::class, 'editContact'])->name('contacts.edit');
    Route::post('/update-contact/{id}', [ContactController::class, 'updateContact'])->name('contacts.update');
    Route::post('/contacts/{id}/upload-avatar', [ContactController::class, 'uploadAvatar'])->name('contacts.uploadAvatar');
    Route::delete('/contacts/{id}/remove-avatar', [ContactController::class, 'removeAvatar'])->name('contacts.removeAvatar');
    
    // Favorite Contacts Feature
    Route::get('/favourites', [ContactController::class, 'favorites'])->name('contacts.favorites');
    Route::post('/toggle-favorite/{id}', [ContactController::class, 'toggleFavorite'])->name('contacts.toggleFavorite');
});
