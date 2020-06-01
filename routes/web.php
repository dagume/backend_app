<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('test', function () {
    event(new App\Events\StatusLiked('Guest'));
    return "Evento enviado!";
});

Route::get('/', function () {
    return view('welcome');
});
Route::get('user-list-pdf', 'RolesController@exportPdf')->name('users.pdf');
Route::resource('roles', 'RolesController');

