<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Notifications\ThreadWasUpdated;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Notification;
use App\Models\{Channel, Reply, Thread, User};
use Illuminate\Foundation\Testing\{RefreshDatabase, WithoutMiddleware};

class ThreadTest extends TestCase
{
    use RefreshDatabase;

    protected $thread;

    protected function setUp(): void
    {
        parent::setUp();

        $this->thread = Thread::factory()->create();
    }

    /** @test */
    public function has_a_path()
    {
        $this->assertEquals("/threads/{$this->thread->channel->slug}/{$this->thread->slug}", $this->thread->path());
        // $this->assertEquals('Illuminate\Database\Eloquent\Collection', $thread->path());

        // $this->assertEquals($thread->path(), $thread->path());
    }

    /** @test */
    public function has_a_creator()
    {
        $this->assertInstanceOf(User::class, $this->thread->creator);
    }

    // /** @test */
    // function an_authenticated_user_may_participate_in_forum_threads()
    // {
    //     $this->withoutExceptionHandling();
    //     // Tenemos un usuario autenticado
    //     $this->be(factory(User::class)->create());

    //     // Y un hilo
    //     $thread = factory(Thread::class)->create();
    //     // Cuando el user añade una replica al hilo
    //     //
    //     $reply = factory(Reply::class)->create();
    //     $this->post('/threads/'. $thread->id .'/replies', $reply->toArray());
    //     // Entonces lo eplicado debe ser visible en la pagina
    //     $this->get($thread->path())
    //          ->assertSee($reply->body);
    // }

    /** @test */
    public function has_a_replies()
    {
        $reply = Reply::factory()->create(['thread_id' => $this->thread]);

        $this->assertInstanceOf(Collection::class, $this->thread->replies);
        $this->assertCount(1, $this->thread->replies);

        // $thread = factory(Thread::class)->create();

        // $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $thread->replies);
    }

    /** @test */
    public function can_add_a_reply()
    {
        $this->thread->addReply([
            'user_id' => 1,
            'body' => 'Foobar',
        ]);

        $this->assertCount(1, $this->thread->replies);
    }

    /** @test */
    public function a_thread_notifies_all_registered_subscribers_when_a_reply_is_added()
    {
        Notification::fake();

        $thread = Thread::factory()->create();
        $user = User::factory()->create();

        //dd($user->first()->id);

        $thread->subscribe($user->first()->id);

        //dd($thread);
        $thread->addReply([
            'body' => 'Foobar',
            'user_id' => $user->first()->id,
        ]);
/* 
        $tdd = $thread
            ->subscribe($user->id)
            ->addReply([
                'body' => 'Foobar',
                'user_id' => $user->id,
            ]); */

        Notification::assertSentTo($user->first(), ThreadWasUpdated::class);

        $this->assertAuthenticated('web');
    }

    /** @test */
    public function belongs_to_a_channel()
    {
        $this->assertInstanceOf(Channel::class, $this->thread->channel);
    }

    /** @test */
    public function a_thread_can_be_subscribed_to()
    {
        $this->actingAs(User::factory()->create());
        $this->thread->subscribe();

        $this->assertEquals(1, $this->thread->subscriptions()
                ->where('user_id', auth()->id())->count());

        $this->assertAuthenticated('web');
    }

    /** @test */
    public function a_thread_can_be_unsubscribed_from()
    {
        $this->actingAs(User::factory()->create());
        $this->thread->subscribe();
        $this->thread->unsubscribe();

        $this->assertEquals(0, $this->thread->subscriptions()->where('user_id', auth()->id())->count());

        $this->assertAuthenticated('web');
        // $this->hasThread()->subscribe($userId = 1)
        //     ->unsubscribe($userId);

        // $this->assertCount(0, $this->hasThread()->subscriptions);
    }

    /** @test */
    public function it_knows_if_the_authenticated_user_is_subscribed_to_it()
    {
        $this->actingAs(User::factory()->create());
        $this->assertFalse($this->thread->isSubscribedTo);

        $this->thread->subscribe();

        $this->assertTrue($this->thread->isSubscribedTo);

        $this->assertAuthenticated('web');
    }

    /** @test */
    public function a_thread_can_check_if_the_authenticated_user_has_read_all_replies()
    {
        $this->actingAs(User::factory()->create());
        $thread = Thread::factory()->create();

        tap(auth()->user(), function ($user) use ($thread) {
            $this->assertTrue($thread->hasUpdatesFor($user));

            $user->read($thread);

            $this->assertFalse($thread->hasUpdatesFor($user));
        });

        $this->assertAuthenticated('web');
    }

    /** @test */
    public function is_sanitized_body_automatically()
    {
        $thread = Thread::factory()->make(['body' => '<script>alert("bad")</script><p>This is okay.</p>']);

        $this->assertEquals("<p>This is okay.</p>", $thread->body);
    }
}
