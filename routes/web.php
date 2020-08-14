<?php

use Illuminate\Support\Facades\Route;
use App\Models\Organisation;

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

Route::get('/', 'Auth\LoginController@showLoginForm');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::group([
    'prefix' => 'users',
    'name' => 'users.'
    ], function() {
        Route::get('/', 'UsersController@index')->name('users');
        Route::get('/create', 'UsersController@add')->name('users.create');
        Route::get('/{user}/edit', 'UsersController@edit')->name('users.edit');
        Route::post('/', 'UsersController@store')->name('users.store');
        Route::put('/{user}', 'UsersController@update')->name('users.update');
        Route::delete('/{user}', 'UsersController@delete')->name('users.delete');
});

Route::group([
    'prefix' => 'organisations',
    'name' => 'organisation.'
    ], function() {
        Route::get('/', 'OrganisationsController@index')
                ->name('organisations')
                ->middleware('can:viewAny, App\Models\Organisation');
        
        Route::view('/create', 'organisations.edit')
                ->name('organisations.create')
                ->middleware('can:create, App\Models\Organisation');
        
        Route::get('/{organisation}/edit', 'OrganisationsController@edit')
                ->name('organisations.edit');
        
        Route::post('/', 'OrganisationsController@store')
                ->name('organisations.store')
                ->middleware('can:create, App\Models\Organisation');
        
        Route::put('/{organisation}', 'OrganisationsController@update')
                ->name('organisations.update');
        
        Route::delete('/delete', 'OrganisationsController@delete')
                ->name('organisations.delete')
                ->middleware('can:delete, App\Models\Organisation');
});