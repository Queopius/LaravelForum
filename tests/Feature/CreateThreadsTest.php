<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Rules\Recaptcha;
use Illuminate\Support\Facades\Gate;
use App\Exceptions\ThrottleException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\Thread\StoreThreadRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\{Activity, Channel, Reply, Thread, User};
use App\Http\Controllers\Threads\CreateThreadsController;

class CreateThreadsTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;
    
    public function setUp(): void
    {
        parent::setUp();

        app()->singleton(Recaptcha::class, function () {
            return \Mockery::mock(Recaptcha::class, function ($m) {
                $m->shouldReceive('passes')->andReturn(true);
            });
        });
    }

    /** @test */
    public function guests_may_not_create_threads()
    {
        $this->expectException(AuthenticationException::class);
        $this->withoutExceptionHandling();
        $this->get('/threads/create')
            ->assertGuest()
            ->assertRedirect('login')
            ->assertStatus(302);

        $this->post('/threads')
            ->assertGuest()
            ->assertRedirect('login')
            ->assertStatus(302);
    }

    /** @test */
    public function new_users_must_first_confirm_their_email_address_before_creating_threads()
    {
        $user = User::factory()->create(['email_verified_at' => null]);

        $this->signIn($user)
            ->post(route('threads'), Thread::make()->toArray())
            ->assertRedirect('email/verify');
    }

    /** @test */
    public function a_authenticated_user_can_create_a_thread()
    {
        $user = User::factory()->create();
        $channel = Channel::factory()->create();

        $this->signIn($user);

        $request = new StoreThreadRequest([
            'title' => 'Nuevo hilo',
            'body' => 'Contenido del nuevo hilo',
            'channel_id' => $channel->id,
        ] + ['g-recaptcha-response' => 'token']);

        (new CreateThreadsController)->store($request);

        $this->assertDatabaseHas('threads', [
            'title' => 'Nuevo hilo',
            'body' => 'Contenido del nuevo hilo',
            'channel_id' => $channel->id,
            'user_id' => $user->id,
        ]);
    }

    /** @test */
    public function a_thread_requires_a_title()
    {
        //$this->expectException(ThrottleException::class);
        Gate::shouldReceive('allows')
            ->with('create', \Mockery::type(\App\Models\Reply::class))
            ->andReturn(false);

        $request = new StoreThreadRequest([
            'title' => '',
            'body' => 'Contenido del nuevo hilo',
            'channel_id' => 1,
        ]+ ['g-recaptcha-response' => 'token']);

        $this->assertFalse($request->authorize());


        $this->expectException(\Illuminate\Validation\ValidationException::class);

        $request->authorize();

        //$request->failedAuthorization();
        /* $user = User::factory()->create();
        $channel = Channel::factory()->create();

        $this->signIn($user);

        $this->publishThread(['title' => null], $user)
            ->assertSessionHasErrors('title');
 */
        /* $request = new StoreThreadRequest([
            'title' => '',
            'body' => 'Contenido del nuevo hilo',
            'channel_id' => $channel->id,
        ] + ['g-recaptcha-response' => 'token']);

        (new CreateThreadsController)->store($request); */


        // Verificamos que no se haya creado ningún hilo en la base de datos
        //$this->assertDatabaseCount('threads', 0);
        /* $this->publishThread(['title' => null])
            ->assertSessionHasErrors('title');
 */
        /* $this->publishThread(['title' => ''])
            ->assertRedirect('threads')
            ->assertSessionHasErrors(['title']); */
    }

    /** @test */
    public function a_thread_requires_a_body()
    {
        $this->publishThread(['body' => ''])
            ->assertRedirect('');
    }

    /** @test */
    public function a_thread_requires_recaptcha_verification()
    {
        unset(app()[Recaptcha::class]);

        $this->publishThread(['g-recaptcha-response' => 'test'])
            ->assertRedirect('');
    }

    /** @test */
    public function a_thread_requires_a_valid_channel()
    {
        Channel::factory()->count(2)->create();

        $this->publishThread(['channel_id' => null])
            ->assertRedirect('');

        $this->publishThread(['channel_id' => 999])
            ->assertRedirect('');
    }

    /** @test */
    public function a_thread_requires_a_unique_slug()
    {
        $this->withoutExceptionHandling();

        $this->signIn();

        $thread = Thread::factory()->create(['title' => 'Foo Title']);

        $this->assertEquals($thread->slug, 'foo-title');

        $thread = $this->postJson(route('threads'), $thread->toArray()  + ['g-recaptcha-response' => 'token'])->json();

        $this->assertEquals("foo-title-{$thread['id']}", $thread['slug']);

        $this->assertAuthenticated('web');

        // $this->signIn($user);

        // $thread = create(Thread::class, ['title' => 'Foo Title']);

        // $this->assertEquals($thread->slug, 'foo-title');

        // $thread = $this->postJson(route('threads'), $thread->toArray() + ['g-recaptcha-response' => 'token'])->json();


        // $this->assertEquals("foo-title-{$thread['id']}", $thread['slug']);
        // dd($this->response->getContent());
    }

    /** @test */
    public function a_thread_with_a_title_that_ends_in_a_number_should_generate_the_proper_slug()
    {
        $this->signIn();

        $thread = Thread::factory()->create(['title' => 'Some Title 24']);

        $thread = $this->postJson(route('threads'), $thread->toArray() + ['g-recaptcha-response' => 'token']);

        $this->assertEquals("some-title-24-{$thread['id']}", $thread['slug']);

        $this->assertAuthenticated('web');
    }

    /** @test */
    public function unauthorized_users_may_not_delete_threads()
    {
        $thread = Thread::factory()->create();

        $this->delete($thread->path())->assertRedirect('/login');

        $this->signIn();
        $this->delete($thread->path())->assertStatus(403);
        $this->assertAuthenticated('web');
    }

    /** @test */
    public function authorized_users_can_delete_threads()
    {
        $this->signIn();

        $thread = Thread::factory()->create(['user_id' => auth()->id()]);
        $reply = Reply::factory()->create(['thread_id' => $thread->id]);

        $response = $this->json('DELETE', $thread->path());

        $response->assertStatus(204);

        $this->assertDatabaseMissing('threads', ['id' => $thread->id]);
        $this->assertDatabaseMissing('replies', ['id' => $reply->id]);

        $this->assertEquals(0, Activity::count());
        $this->assertAuthenticated('web');
    }

    protected function publishThread($attributes = [], $user = null)
    {
         return $this->signIn($user)->postJson(route('threads.store'), Thread::make($attributes)->toArray()  + ['g-recaptcha-response' => 'token']);
    }
}
