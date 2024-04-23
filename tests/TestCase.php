<?php

namespace Tests;

use App\Models\User;
use Illuminate\Testing\TestResponse;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Contracts\Auth\Authenticatable as UserContract;
use Illuminate\Foundation\Testing\{RefreshDatabase, TestCase as BaseTestCase};

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, DetectRepeatedQueries, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->addTestResponseMacros();

        $this->withoutExceptionHandling();

        $this->enableQueryLog();
    }

    protected function actingAsUser($user = null)
    {
        if ($user == null) {
            $user = $this->createUser();
        }

        return $this->actingAs($user);
    }

    protected function createUser(array $attributes = [])
    {
        return User::factory()->create($attributes);
    }

    protected function signIn($user = null)
    {
        return tap($this)->actingAs($user ?? User::factory()->create());
    }

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
