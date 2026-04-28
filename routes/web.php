<?php

use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ====================== GUEST / AUTH ROUTES ======================
Route::middleware('guest')->group(function () {

    Route::get('/login', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])
         ->name('login');

    Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'login']);

    Route::get('/register', [App\Http\Controllers\Auth\RegisterController::class, 'showRegister'])
         ->name('register');

    Route::post('/register', [App\Http\Controllers\Auth\RegisterController::class, 'register']);

    // Forgot Password Routes
    Route::get('/forgot-password',  
        [App\Http\Controllers\Auth\ForgotPasswordController::class, 'showForm'])
        ->name('axiom.password.request');

    Route::post('/forgot-password', 
        [App\Http\Controllers\Auth\ForgotPasswordController::class, 'verifyEmail'])
        ->name('axiom.password.verify');

    Route::get('/reset-password',   
        [App\Http\Controllers\Auth\ForgotPasswordController::class, 'showResetForm'])
        ->name('axiom.password.reset.form');

    Route::post('/reset-password',  
        [App\Http\Controllers\Auth\ForgotPasswordController::class, 'reset'])
        ->name('axiom.password.reset.submit');
});

// Redirect root to login
Route::get('/', function () {
    return redirect('/login');
});

// Logout
Route::post('/logout', [App\Http\Controllers\Auth\LogoutController::class, 'logout'])
     ->name('logout')
     ->middleware('auth');

// ====================== ADMIN ROUTES ======================
Route::middleware(['auth', 'role:Admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

        Route::get('/users',                    [App\Http\Controllers\Admin\UserController::class, 'index'])     ->name('users.index');
        Route::get('/users/create',             [App\Http\Controllers\Admin\UserController::class, 'create'])    ->name('users.create');
        Route::post('/users',                   [App\Http\Controllers\Admin\UserController::class, 'store'])     ->name('users.store');
        Route::get('/users/{user}/edit',        [App\Http\Controllers\Admin\UserController::class, 'edit'])      ->name('users.edit');
        Route::put('/users/{user}',             [App\Http\Controllers\Admin\UserController::class, 'update'])    ->name('users.update');
        Route::patch('/users/{user}/approve',   [App\Http\Controllers\Admin\UserController::class, 'approve'])   ->name('users.approve');
        Route::patch('/users/{user}/reject',    [App\Http\Controllers\Admin\UserController::class, 'reject'])    ->name('users.reject');
        Route::patch('/users/{user}/deactivate',[App\Http\Controllers\Admin\UserController::class, 'deactivate'])->name('users.deactivate');

        Route::get('/books/catalog-pdf', [App\Http\Controllers\Admin\BookController::class, 'catalogPdf'])
            ->name('books.catalog-pdf');
        Route::resource('books', App\Http\Controllers\Admin\BookController::class)->except(['show','create','edit']);

        Route::get('/borrows',     [App\Http\Controllers\Admin\BorrowController::class, 'index'])->name('borrows.index');
        Route::get('/borrows/pdf', [App\Http\Controllers\Admin\BorrowController::class, 'pdf'])  ->name('borrows.pdf');

        Route::resource('departments', App\Http\Controllers\Admin\DepartmentController::class)
            ->except(['show','create','edit']);

        Route::get('/settings', [App\Http\Controllers\Admin\SettingsController::class, 'index']) ->name('settings');
        Route::put('/settings', [App\Http\Controllers\Admin\SettingsController::class, 'update'])->name('settings.update');

        Route::get('/profile',          [App\Http\Controllers\Admin\ProfileController::class, 'index'])         ->name('profile');
        Route::put('/profile/photo',    [App\Http\Controllers\Admin\ProfileController::class, 'updatePhoto'])   ->name('profile.photo');
        Route::put('/profile/password', [App\Http\Controllers\Admin\ProfileController::class, 'updatePassword'])->name('profile.password');
    });

// ====================== LIBRARIAN ROUTES ======================
Route::middleware(['auth', 'role:Librarian'])
    ->prefix('librarian')
    ->name('librarian.')
    ->group(function () {

        Route::get('/dashboard', [App\Http\Controllers\Librarian\DashboardController::class, 'index'])->name('dashboard');

        Route::get('/books/catalog-pdf', [App\Http\Controllers\Librarian\BookController::class, 'catalogPdf'])
            ->name('books.catalog-pdf');
        Route::get('/books',             [App\Http\Controllers\Librarian\BookController::class, 'index']) 
            ->name('books.index');
        Route::put('/books/{book}',      [App\Http\Controllers\Librarian\BookController::class, 'update'])
            ->name('books.update');

        Route::get('/borrows',                        [App\Http\Controllers\Librarian\BorrowController::class, 'index'])  
            ->name('borrows.index');
        Route::patch('/borrows/{borrowing}/approve',  [App\Http\Controllers\Librarian\BorrowController::class, 'approve'])
            ->name('borrows.approve');
        Route::patch('/borrows/{borrowing}/reject',   [App\Http\Controllers\Librarian\BorrowController::class, 'reject']) 
            ->name('borrows.reject');
        Route::get('/borrows/pdf',                    [App\Http\Controllers\Librarian\BorrowController::class, 'pdf'])    
            ->name('borrows.pdf');

        Route::get('/profile',          [App\Http\Controllers\Librarian\ProfileController::class, 'index'])         ->name('profile');
        Route::put('/profile/photo',    [App\Http\Controllers\Librarian\ProfileController::class, 'updatePhoto'])   ->name('profile.photo');
        Route::put('/profile/password', [App\Http\Controllers\Librarian\ProfileController::class, 'updatePassword'])->name('profile.password');
    });

// ====================== STUDENT & FACULTY SHARED ROUTES ======================

// STUDENT ROUTES
Route::middleware(['auth', 'role:Student'])
    ->prefix('student')
    ->name('student.')
    ->group(function () {

        Route::get('/dashboard', [App\Http\Controllers\Student\DashboardController::class, 'index'])->name('dashboard');

        Route::get('/books', [App\Http\Controllers\Student\BookController::class, 'index'])->name('books.index');
        Route::post('/books/{book}/borrow', [App\Http\Controllers\Student\BookController::class, 'borrow'])
            ->name('books.borrow');

        Route::get('/my-books', [App\Http\Controllers\Student\MyBooksController::class, 'index'])->name('my-books.index');
        Route::delete('/my-books/{borrowing}/cancel', [App\Http\Controllers\Student\MyBooksController::class, 'cancel'])
            ->name('my-books.cancel');
        Route::get('/my-books/{borrowing}/read', [App\Http\Controllers\Student\BookAccessController::class, 'read'])
            ->name('my-books.read');

        Route::get('/profile',          [App\Http\Controllers\Student\ProfileController::class, 'index'])         ->name('profile');
        Route::put('/profile/photo',    [App\Http\Controllers\Student\ProfileController::class, 'updatePhoto'])   ->name('profile.photo');
        Route::put('/profile/password', [App\Http\Controllers\Student\ProfileController::class, 'updatePassword'])->name('profile.password');
    });

// FACULTY ROUTES (Reusing Student Controllers)
Route::middleware(['auth', 'role:Faculty'])
    ->prefix('faculty')
    ->name('faculty.')
    ->group(function () {

        Route::get('/dashboard', [App\Http\Controllers\Student\DashboardController::class, 'index'])->name('dashboard');

        Route::get('/books', [App\Http\Controllers\Student\BookController::class, 'index'])->name('books.index');
        Route::post('/books/{book}/borrow', [App\Http\Controllers\Student\BookController::class, 'borrow'])
            ->name('books.borrow');

        Route::get('/my-books', [App\Http\Controllers\Student\MyBooksController::class, 'index'])->name('my-books.index');
        Route::delete('/my-books/{borrowing}/cancel', [App\Http\Controllers\Student\MyBooksController::class, 'cancel'])
            ->name('my-books.cancel');
        Route::get('/my-books/{borrowing}/read', [App\Http\Controllers\Student\BookAccessController::class, 'read'])
            ->name('my-books.read');

        Route::get('/profile',          [App\Http\Controllers\Student\ProfileController::class, 'index'])         ->name('profile');
        Route::put('/profile/photo',    [App\Http\Controllers\Student\ProfileController::class, 'updatePhoto'])   ->name('profile.photo');
        Route::put('/profile/password', [App\Http\Controllers\Student\ProfileController::class, 'updatePassword'])->name('profile.password');
    });

// ====================== NOTIFICATION ROUTES ======================
Route::middleware(['auth'])->group(function () {
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markRead'])
        ->name('notifications.read');

    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])
        ->name('notifications.readAll');
});