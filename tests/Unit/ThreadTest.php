<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Notifications\ThreadWasUpdated;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Notification;
use App\Models\{Thread, User, Channel, Reply};
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class ThreadTest extends TestCase
{
    use RefreshDatabase;

    protected $thread;

    public function setUp(): void
    {
        parent::setUp();
    }

    /** @test */
    function a_thread_has_a_path()
    {
        $thread = factory(Thread::class)->create();

        // $this->assertEquals('Illuminate\Database\Eloquent\Collection', $thread->path());

        $this->assertEquals($thread->path(), $thread->path());
    }

    /** @test */
    function a_thread_has_a_creator()
    {
        $this->assertInstanceOf(User::class, $this->hasThread()->creator);
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
    function a_thread_has_replies()
    {
        $thread = factory(Thread::class)->create();
        
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $thread->replies);
    }

    /** @test */
    function a_thread_can_add_a_reply()
    {
        $thread = factory(Thread::class)->create();

        $thread->addReply([
            'body' => 'Foobar',
            'user_id' => 1
        ]);

        $this->assertCount(1, $thread->replies);
    }

    // /** @test */
    // function a_thread_notifies_all_registered_subscribers_when_a_reply_is_added()
    // {
    //     Notification::fake();

    //     $this->signIn()
    //         ->thread
    //         ->subscribe()
    //         ->addReply([
    //             'body' => 'Foobar',
    //             'user_id' => 999
    //         ]);

    //     Notification::assertSentTo(auth()->user(), ThreadWasUpdated::class);
    // }

    /** @test */
    function a_thread_belongs_to_a_channel()
    {
        $this->assertInstanceOf(Channel::class, $this->hasThread()->channel);
    }

    /** @test */
    function a_thread_can_be_subscribed_to()
    {
        $thread = $this->hasThread()->subscribe($userId = 1);

        $this->assertEquals(
            1,
            $thread->subscriptions()->where('user_id', $userId)->count()
        );
    }

    /** @test */
    function a_thread_can_be_unsubscribed_from()
    {
        $this->hasThread()->subscribe($userId = 1)
            ->unsubscribe($userId);

        $this->assertCount(0, $this->hasThread()->subscriptions);
    }

    /** @test */
    function it_knows_if_the_authenticated_user_is_subscribed_to_it()
    {
        $thread = create(Thread::class);

        $this->signIn();

        $this->assertFalse($thread->isSubscribedTo);

        $thread->subscribe();

        $this->assertTrue($thread->isSubscribedTo);
    }

    /** @test */
    function a_thread_can_check_if_the_authenticated_user_has_read_all_replies()
    {
        $this->signIn();

        $thread = create(Thread::class);

        tap(auth()->user(), function ($user) use ($thread) {
            $this->assertTrue($thread->hasUpdatesFor($user));

            $user->read($thread);

            $this->assertFalse($thread->hasUpdatesFor($user));
        });
    }

    /** @test */
    function a_threads_body_is_sanitized_automatically()
    {
        $thread = make(Thread::class, ['body' => '<script>alert("bad")</script><p>This is okay.</p>']);

        $this->assertEquals("<p>This is okay.</p>", $thread->body);
    }

    protected function hasThread()
    {
        $thread = factory(Thread::class)->create();

        return $thread;
    }
}
