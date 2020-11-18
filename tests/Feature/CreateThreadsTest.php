<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Rules\Recaptcha;
use Illuminate\Foundation\Testing\WithoutMiddleware;
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
        //$this->withExceptionHandling();
        $this->get('/threads/create')
            ->assertRedirect('login');

        $this->post('threads')
            ->assertRedirect('login');
    }
 
    /** @test */
    function new_users_must_first_confirm_their_email_address_before_creating_threads()
    {
        $user = create(User::class, ['email_verified_at' => null]);

        $this->signIn($user)
            ->post(route('threads'), make(Thread::class)->toArray())
            ->assertRedirect(route('threads'))
            ->assertSessionHas('flash', 'You must first confirm your email address.');
    }

    /** @test */
    function a_authenticated_user_can_create_a_thread()
    {
        $response = $this->publishThread(['title', 'body']);

        $this->get($response->headers->get('Location'))
            ->assertSee('title')
            ->assertSee('body');
    }

    /** @test */
    function a_thread_requires_a_title()
    {
        $this->publishThread(['title' => ''])
            ->assertRedirect('threads');

        // $this->publishThread(['title' => ''])
        //     ->assertRedirect('threads')
        //     ->assertSessionHasErrors(['title']);
    }

    /** @test */
    function a_thread_requires_a_body()
    {
        $this->publishThread(['body' => ''])
            ->assertRedirect('threads');
    }

    /** @test */
    function a_thread_requires_recaptcha_verification()
    {
        unset(app()[Recaptcha::class]);

        $this->publishThread(['g-recaptcha-response' => 'test'])
            ->assertRedirect('threads');
    }

    /** @test */
    function a_thread_requires_a_valid_channel()
    {
        factory(Channel::class, 2)->create();

        $this->publishThread(['channel_id' => null])
            ->assertRedirect('threads');

        $this->publishThread(['channel_id' => 999])
            ->assertRedirect('threads');
    }

    /** @test */
    function a_thread_requires_a_unique_slug()
    {
        $this->actingAsUser();

        $thread = create(Thread::class, ['title' => 'Foo Title']);

        $this->assertEquals($thread->slug, 'foo-title');

        $thread = $this->postJson(route('threads'), $thread->toArray() + ['g-recaptcha-response' => 'token'])->json();


        $this->assertEquals("foo-title-{$thread['id']}", $thread['slug']);
        // dd($this->response->getContent());
    }

    /** @test */
    function a_thread_with_a_title_that_ends_in_a_number_should_generate_the_proper_slug()
    {
        $this->signIn();

        $thread = create(Thread::class, ['title' => 'Some Title 24']);

        $thread = $this->postJson(route('threads'), $thread->toArray() + ['g-recaptcha-response' => 'token']);

        $this->assertEquals("some-title-24-{$thread['id']}", $thread['slug']);
    }

    /** @test */
    function unauthorized_users_may_not_delete_threads()
    {
        $thread = create(Thread::class);

        $this->delete($thread->path())->assertRedirect('/login');

        $this->signIn();
        $this->delete($thread->path())->assertStatus(403);
    }

    /** @test */
    function authorized_users_can_delete_threads()
    {        
        $this->signIn();

        $thread = create(Thread::class, ['user_id' => auth()->id()]);
        $reply = create(Reply::class, ['thread_id' => $thread->id]);

        $response = $this->json('DELETE', $thread->path());

        $response->assertStatus(204);

        $this->assertDatabaseMissing('threads', ['id' => $thread->id]);
        $this->assertDatabaseMissing('replies', ['id' => $reply->id]);

        $this->assertEquals(0, Activity::count());
    }

    protected function publishThread($attributes = [], $user = null)
    {
         return $this->signIn($user)->post('threads', make(Thread::class, $attributes)->toArray()  + ['g-recaptcha-response' => 'token']);
    }
}
