<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Events\ThreadReceivedNewReply;
use App\Notifications\ThreadWasUpdated;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Notification;
use App\Models\{Channel, Reply, Thread, User};
use Illuminate\Foundation\Testing\RefreshDatabase;

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
        $this->assertEquals(
            "/threads/{$this->thread->channel->slug}/{$this->thread->slug}",
            $this->thread->path()
        );
    }

    /** @test */
    public function has_a_creator()
    {
        $this->assertInstanceOf(User::class, $this->thread->creator);
    }

    /** @test */
    public function has_a_replies()
    {
        Reply::factory()->create(['thread_id' => $this->thread]);

        $this->assertInstanceOf(Collection::class, $this->thread->replies);
        $this->assertCount(1, $this->thread->replies);
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
        $this->signIn($user = User::factory()->create());
        $thread = Thread::factory()->create();
        $reply = Reply::factory()->create(['thread_id' => $thread->id]);

        $thread->subscribe($user->first()->id);
        $thread->addReply([
            'body' => 'Foobar',
            'user_id' => $user->first()->id,
        ]);

        Notification::fake();
        event(new ThreadReceivedNewReply($thread, $reply));
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
        $this->signin(User::factory()->create());
        $this->thread->subscribe();

        $this->assertEquals(1, $this->thread->subscriptions()
                ->where('user_id', auth()->id())->count());

        $this->assertAuthenticated('web');
    }

    /** @test */
    public function a_thread_can_be_unsubscribed_from()
    {
        $this->signin(User::factory()->create());
        $this->thread->subscribe();
        $this->thread->unsubscribe();

        $this->assertEquals(0, $this->thread->subscriptions()->where('user_id', auth()->id())->count());

        $this->assertAuthenticated('web');
    }

    /** @test */
    public function it_knows_if_the_authenticated_user_is_subscribed_to_it()
    {
        $this->signin(User::factory()->create());
        $this->assertFalse($this->thread->isSubscribedTo);

        $this->thread->subscribe();

        $this->assertTrue($this->thread->isSubscribedTo);

        $this->assertAuthenticated('web');
    }

    /** @test */
    public function a_thread_can_check_if_the_authenticated_user_has_read_all_replies()
    {
        $this->signin(User::factory()->create());
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
