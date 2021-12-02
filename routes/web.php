<?php

use Illuminate\Support\Facades\{Auth, Route};
use App\Http\Controllers\ThreadsController;
use App\Http\Controllers\{
	ThreadSubscriptionsController,
	HomeController,
	SearchController,
	RepliesController,
	ProfilesController,
	Api\UsersController,
	Api\UserAvatarController,
	FavoritesController,
	BestRepliesController,
	LockedThreadsController,
	UserNotificationsController
};

Auth::routes(['verify' => true]);

Route::get('/', function () {
    return view('welcome');
});

Route::get('/home', [HomeController::class, 'index'])->name('home');
/**
 * Reoute Thread Searach
 */
Route::get('threads/search', [SearchController::class, 'show']);
/**
 * Routes Thread
 */
Route::group(['prefix' => 'threads'], function () {
	Route::get('/', [ThreadsController::class, 'index'])->name('threads');
	Route::name('threads.')->group(function () {
		Route::resource('/', ThreadsController::class)
			->parameters(['/' => 'thread'])
			->only(['create', 'edit'])
			->middleware('auth');

        Route::get('{channel}/{thread}', [ThreadsController::class, 'show'])
        	->name('show');

        Route::post('/', [ThreadsController::class, 'store'])
        	->name('store')
        	->middleware(['verified', 'auth']);

        Route::delete('{channel}/{thread}', [ThreadsController::class, 'destroy'])
        	->name('destroy')
        	->middleware('auth');

        Route::patch('{channel}/{thread}', [ThreadsController::class, 'update']);
	});

	/**
	 * Routes Thread Subscripions
	 */
	Route::post('{channel}/{thread}/subscriptions', [ThreadSubscriptionsController::class, 'store'])
		->middleware('auth');
	Route::delete('{channel}/{thread}/subscriptions', [ThreadSubscriptionsController::class, 'destroy'])
		->middleware('auth');
	/**
	 * Routes Reply
	 */
	Route::get('{channel}/{thread}/replies', [RepliesController::class, 'index']);
	Route::post('{channel}/{thread}/replies', [RepliesController::class, 'store'])
		->name('replies.store');
	Route::get('{channel}', [ThreadsController::class, 'index'])
		->name('thread.index');
});

/**
 * Routes Reply
 */
Route::group(['middleware' => 'auth', 'prefix' => 'replies/{reply}'], function () {
	Route::post('/favorites', [FavoritesController::class, 'store']);
	Route::delete('/favorites', [FavoritesController::class, 'destroy']);
	Route::post('/best', [BestRepliesController::class, 'store'])
		->name('best-replies.store');
	Route::delete('', [RepliesController::class, 'destroy'])
		->name('replies.destroy');
	Route::patch('', [RepliesController::class, 'update']);
});
/**
 * Routes Profiles
 */
Route::group(['prefix' => 'profiles/{user}'], function () {
	Route::get('', [ProfilesController::class, 'show'])
		->name('profile');
	Route::get('/notifications', [UserNotificationsController::class, 'index']);
	Route::delete('/notifications/{notification}', [UserNotificationsController::class, 'destroy'])
		->middleware('auth');
});
/**
 * Routes Api Users
 */
Route::get('api/users', [UsersController::class, 'index']);
Route::post('api/users/{user}/avatars', [UserAvatarController::class, 'store'])
	->middleware('auth')
	->name('avatars');
/**
 * Routes Locked Thread
 */
Route::post('locked-threads/{thread}', [LockedThreadsController::class, 'store'])
	->name('locked-threads.store')
	->middleware('admin');
Route::delete('locked-threads/{thread}', [LockedThreadsController::class, 'destroy'])
	->name('locked-threads.destroy')
	->middleware('admin');