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

Route::get('/', function () {
    return view('welcome');
});

Route::match(['get', 'post'], '/botman', 'BotManController@handle');
Route::get('/botman/tinker', 'BotManController@tinker');

Route::match(['get','post'],'/facebook', 'BotManController@facebook');
Route::match(['get','post'],'/telegram', 'BotManController@telegram');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/admin/dashboard', 'AdminController@dashboard');
Route::get('/admin/complaints', 'AdminController@complaints');
Route::get('/admin/complaint/{id?}', 'AdminController@complaint');
Route::get('/admin/offers', 'AdminController@offers');
Route::get('/admin/offer/{id?}', 'AdminController@offer');
Route::post('/admin/offer/{id?}', 'AdminController@addOffer');
Route::get('/admin/delete/{id}', 'AdminController@delete');
Route::post('/admin/reply', 'AdminController@reply');
