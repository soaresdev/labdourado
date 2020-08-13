<?php

use Illuminate\Support\Facades\Route;

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

/** Formulário de Login */
Route::get('/', 'AuthController@showLoginForm')->name('showLogin');
Route::post('/login', 'AuthController@login')->name('login');
Route::post('/logout', 'AuthController@logout')->name('logout');

Route::prefix(config('constants.dashboard.path'))->middleware('auth')->group(function() {
    /** Dashboard Home */
    Route::prefix('api')->group(function(){
        Route::prefix('/users')->group(function(){
            Route::get('/', 'UserController@index')->name('users.index');
            Route::post('/store', 'UserController@store')->name('users.store');
            Route::put('/{id}/update', 'UserController@update')->name('users.update');
            Route::delete('/{id}/delete', 'UserController@delete')->name('users.delete');
        });
        Route::prefix('/providers')->group(function(){
            Route::get('/', 'ProviderController@index')->name('providers.index');
            Route::post('/store', 'ProviderController@store')->name('providers.store');
            Route::put('/{id}/update', 'ProviderController@update')->name('providers.update');
            Route::delete('/{id}/delete', 'ProviderController@delete')->name('providers.delete');
        });
        Route::prefix('/patients')->group(function(){
            Route::get('/', 'PatientController@index')->name('patients.index');
            Route::post('/store', 'PatientController@store')->name('patients.store');
            Route::put('/{id}/update', 'PatientController@update')->name('patients.update');
            Route::delete('/{id}/delete', 'PatientController@delete')->name('patients.delete');
        });
        Route::prefix('/doctors')->group(function(){
            Route::get('/', 'DoctorController@index')->name('doctors.index');
            Route::post('/store', 'DoctorController@store')->name('doctors.store');
            Route::put('/{id}/update', 'DoctorController@update')->name('doctors.update');
            Route::delete('/{id}/delete', 'DoctorController@delete')->name('doctors.delete');
        });
        Route::prefix('/operators')->group(function(){
            Route::get('/', 'OperatorController@index')->name('operators.index');
            Route::get('/select', 'OperatorController@indexData')->name('operators.indexData');
            Route::post('/store', 'OperatorController@store')->name('operators.store');
            Route::put('/{id}/update', 'OperatorController@update')->name('operators.update');
            Route::delete('/{id}/delete', 'OperatorController@delete')->name('operators.delete');
        });
    });
    Route::get('/{view?}', 'HomeController')->where('view', '(.*)')->name('home');
});
