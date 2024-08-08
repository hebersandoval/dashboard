<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

Route::get('/', function (Request $request) {
    return inertia('Home', [
        // Make $request available inside clousure - 'use'
        'users' => User::when($request->search, function ($query) use ($request) {
            $query
                ->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('email', 'like', '%' . $request->search . '%');
        })->paginate(5)->withQueryString(),

        // Send it as a prop
        'searchTerm' => $request->search,

        // Auth
        'can' => [
            'delete_user' => Auth::user() ? Auth::user()->can('delete', User::class) : null
        ]
    ]);
})->name('home');

Route::middleware('auth')->group(function () {
    Route::inertia('/dashboard', 'Dashboard')->name('dashboard');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

Route::middleware('guest')->group(function () {
    Route::inertia('/register', 'Auth/Register')->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    Route::inertia('/login', 'Auth/Login')->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});




// Route::get('/about', function () {
//     // Send props to the view in an []
//     return inertia('About', ['user' => 'Jon']);
// })->name('about');

// // This is a shorter version of above, if you don't need a controller
// Route::inertia('/contact', 'Contact', ['phone' => '800-233-5511'])->name('contact');
