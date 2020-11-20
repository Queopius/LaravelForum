<?php

use Illuminate\Support\Facades\Route;

Auth::routes(['verify' => true]);

Route::get('/', function () {
    return view('welcome');
});

Route::get('/home', 'HomeController@index')->name('home');
/**
 * Reoute Thread Searach
 */
Route::get('threads/search', 'SearchController@show');
/**
 * Routes Thread
 */
Route::group(['prefix' => 'threads'], function () {
	Route::get('', 'ThreadsController@index')->name('threads');
	Route::name('threads.')->group(function () {
		Route::resource('', 'ThreadsController')
			->parameters(['' => 'thread'])
			->only(['create', 'edit'])
			->middleware('auth');

        Route::get('{channel}/{thread}', 'ThreadsController@show')
        	->name('show');

        Route::post('', 'ThreadsController@store')
        	->name('store')
        	->middleware(['verified', 'auth']);

        Route::delete('{channel}/{thread}', 'ThreadsController@destroy')
        	->name('destroy')
        	->middleware('auth');

        Route::patch('{channel}/{thread}', 'ThreadsController@update');
	});

	/**
	 * Routes Thread Subscripions
	 */
	Route::post('{channel}/{thread}/subscriptions', 'ThreadSubscriptionsController@store')
		->middleware('auth');
	Route::delete('{channel}/{thread}/subscriptions', 'ThreadSubscriptionsController@destroy')
		->middleware('auth');
	/**
	 * Routes Reply
	 */
	Route::get('{channel}/{thread}/replies', 'RepliesController@index');
	Route::post('{channel}/{thread}/replies', 'RepliesController@store')
		->name('replies.store');
	Route::get('{channel}', 'ThreadsController@index')
		->name('thread.index');
});

/**
 * Routes Reply
 */
Route::group(['middleware' => 'auth', 'prefix' => 'replies/{reply}'], function () {
	Route::post('/favorites', 'FavoritesController@store');
	Route::delete('/favorites', 'FavoritesController@destroy');
	Route::post('/best', 'BestRepliesController@store')
		->name('best-replies.store');
	Route::delete('', 'RepliesController@destroy')
		->name('replies.destroy');
	Route::patch('', 'RepliesController@update');
});
/**
 * Routes Profiles
 */
Route::group(['prefix' => 'profiles/{user}'], function () {
	Route::get('', 'ProfilesController@show')
		->name('profile');
	Route::get('/notifications', 'UserNotificationsController@index');
	Route::delete('/notifications/{notification}', 'UserNotificationsController@destroy')
		->middleware('auth');
});
/**
 * Routes Api Users
 */
Route::get('api/users', 'Api\UsersController@index');
Route::post('api/users/{user}/avatars', 'Api\UserAvatarController@store')
	->middleware('auth')
	->name('avatars');
/**
 * Routes Locked Thread
 */
Route::post('locked-threads/{thread}', 'LockedThreadsController@store')
	->name('locked-threads.store')
	->middleware('admin');
Route::delete('locked-threads/{thread}', 'LockedThreadsController@destroy')
	->name('locked-threads.destroy')
	->middleware('admin');