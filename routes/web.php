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

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

// Search Routes for Laravel Scout
Route::get('/posts/search', 'PostController@search')->name('posts.search');
Route::get('/posts/searchjs', 'PostController@searchjs')->name('posts.searchjs');
Route::resource('/posts', 'PostController');

// Social Logins for Laravel Socialite
Route::get('login/{provider}', 'Auth\SocialAccountController@redirectToProvider');
Route::get('login/{provider}/callback', 'Auth\SocialAccountController@handleProviderCallback');

// Payment URLS for Laravel Cashier
Route::get('pay/{plan}', 'PaymentsController@pay')->name('pay');
Route::post('pay/{plan}', 'PaymentsController@pay');
Route::get('cancel', 'PaymentsController@cancel')->name('cancel');
Route::get('user/invoice/{invoiceId}', 'PaymentsController@invoice');
