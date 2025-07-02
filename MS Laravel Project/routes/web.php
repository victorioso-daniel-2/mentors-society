<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/vue-test', function () {
    return view('vue-test');
});

Route::get('/student_dashboard', function () {
    return view('student_dashboard');
});

Route::get('/student-dashboard', function () {
    return view('student_dashboard');
});

Route::get('/officer_dashboard', function () {
    return view('officer_dashboard');
});

Route::get('/student_management', function () {
    return view('student_management');
});

Route::get('/dashboard', function () {
    return view('officer_dashboard');
});

Route::get('/officer-dashboard', function () {
    return view('officer_dashboard');
});
