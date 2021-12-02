<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Rules\Recaptcha;
use App\Models\{User, Thread, Channel, Activity, Reply};

class CreateThreadsTest extends TestCase
{
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
    function guests_may_not_create_threads()
    {
        //$this->withoutExceptionHandling();
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
    function new_users_must_first_confirm_their_email_address_before_creating_threads()
    {
        /* $this->publishThread([], User::create([
            'name' => 'John Doe',
            'email' => 'jhon@mail.com',
            'password' => bcrypt('password'),
            'email_verified_at' => null
            ])
        )
            ->assertRedirect('email/verify'); */
            
        $user = User::factory()->create(['email_verified_at' => null]);

        $this->signIn($user)
            ->post(route('threads'), Thread::make()->toArray())
            ->assertRedirect('email/verify');
            /* ->assertSessionHas('flash', 'You must first confirm your email address.'); */
    }

    /** @test */
    function a_authenticated_user_can_create_a_thread()
    {
        $this
            ->followingRedirects()
            ->publishThread(['title', 'body'])
            ->assertSee('title')
            ->assertSee('body');
        /* $response = $this->publishThread(['title', 'body']);

        $this->get($response->headers->get('Location'))
            ->assertSee('title')
            ->assertSee('body'); */
    }

    /** @test */
    function a_thread_requires_a_title()
    {
        /* $this->publishThread(['title' => null])
            ->assertSessionHasErrors('title');
 */
        $this->publishThread(['title' => ''])
            ->assertRedirect('threads')
            ->assertSessionHasErrors(['title']);
    }

    /** @test */
    function a_thread_requires_a_body()
    {
        $this->publishThread(['body' => ''])
            ->assertRedirect('');
    }

    /** @test */
    function a_thread_requires_recaptcha_verification()
    {
        unset(app()[Recaptcha::class]);

        $this->publishThread(['g-recaptcha-response' => 'test'])
            ->assertRedirect('');
    }

    /** @test */
    function a_thread_requires_a_valid_channel()
    {
        Channel::factory()->count(2)->create();

        $this->publishThread(['channel_id' => null])
            ->assertRedirect('');

        $this->publishThread(['channel_id' => 999])
            ->assertRedirect('');
    }

    /** @test */
    function a_thread_requires_a_unique_slug()
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
    function a_thread_with_a_title_that_ends_in_a_number_should_generate_the_proper_slug()
    {
        $this->signIn();

        $thread = Thread::factory()->create(['title' => 'Some Title 24']);

        $thread = $this->postJson(route('threads'), $thread->toArray() + ['g-recaptcha-response' => 'token']);

        $this->assertEquals("some-title-24-{$thread['id']}", $thread['slug']);

        $this->assertAuthenticated('web');
    }

    /** @test */
    function unauthorized_users_may_not_delete_threads()
    {
        $thread = Thread::factory()->create();

        $this->delete($thread->path())->assertRedirect('/login');

        $this->signIn();
        $this->delete($thread->path())->assertStatus(403);
        $this->assertAuthenticated('web');
    }

    /** @test */
    function authorized_users_can_delete_threads()
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
         return $this->signIn($user)->post('threads', Thread::make($attributes)->toArray()  + ['g-recaptcha-response' => 'token']);
    }
}
