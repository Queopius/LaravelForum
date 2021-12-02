<?php

namespace Tests;

use App\Exceptions\Handler;
use Illuminate\Support\Str;
use App\Models\{User, Admin};
use Illuminate\Support\Facades\DB;
use Illuminate\Testing\TestResponse;
use Illuminate\Foundation\Testing\Artisan;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Contracts\Auth\Authenticatable as UserContract;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, DetectRepeatedQueries, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->addTestResponseMacros();
        
        //$this->disableExceptionHandling();

        $this->withoutExceptionHandling();

        $this->enableQueryLog();
    }

    /* protected function actingAsAdmin($admin = null)
    {
        if ($admin == null) {
            $admin = $this->createAdmin();
        }

        return $this->actingAs($admin, 'admin');
    } */

    protected function actingAsUser($user = null)
    {
        if ($user == null) {
            $user = $this->createUser();
        }

        return $this->actingAs($user);
    }

    /* protected function createAdmin(array $attributes = [])
    {
        return Admin::factory()->create($attributes);
    } */

    protected function createUser(array $attributes = [])
    {
        return User::factory()->create($attributes);
    }

    protected function signIn($user = null)
    {
        return tap($this)->actingAs($user ?? User::factory()->create());
    }

    // Hat tip, @adamwathan.
    // protected function disableExceptionHandling()
    // {
    //     $this->oldExceptionHandler = $this->app->make(ExceptionHandler::class);

    //     $this->app->instance(ExceptionHandler::class, new class extends Handler {
    //         public function __construct() {}
    //         public function report(\Throwable $e) {}
    //         public function render($request, \Throwable $e) {
    //             throw $e;
    //         }
    //     });
    // }

    /**
     * Set the currently logged in user for the application.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  string|null  $driver
     * @return void
     */
    public function be(UserContract $user, $driver = null)
    {
        $this->app['auth']->guard($driver)->setUser($user);
    }

    protected function assertDatabaseEmpty($table, $connection = null)
    {
        $total = $this->getConnection($connection)->table($table)->count();
        $this->assertSame(0, $total, sprintf(
            "Failed asserting the table [%s] is empty. %s %s found.", $table, $total, Str::plural('row', $total)
        ));
    }

    protected function tearDown(): void
    {
        $this->flushQueryLog();

        parent::tearDown();
    }

    protected function withExceptionHandling()
    {
        $this->app->instance(ExceptionHandler::class, $this->oldExceptionHandler);

        return $this;
    }

    protected function addTestResponseMacros()
    {
        TestResponse::macro('viewData', function ($key) {
            $this->ensureResponseHasView();
            $this->assertViewHas($key);
            return $this->original->$key;
        });

        TestResponse::macro('assertViewCollection', function ($var) {
            return new TestCollectionData($this->viewData($var));
        });
    }
}
