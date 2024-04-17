<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\{Channel, Reply, Thread, User};

class ReadThreadsTest extends TestCase
{
    protected $thread;

    public function setUp(): void
    {
        parent::setUp();

        $this->thread = Thread::factory()->create();
    }

    /** @test */
    public function a_user_can_view_all_threads()
    {
        $this->get('/threads')
            ->assertSee($this->thread->title);
    }

    /** @test */
    public function a_user_can_read_a_single_thread()
    {
        $this->get($this->thread->path())
            ->assertSee($this->thread->title);
    }

    /** @test */
    public function a_user_can_filter_threads_according_to_a_channel()
    {
        $this->withoutExceptionHandling();

        $channel = Channel::factory()->create();
        $threadInChannel = Thread::factory()->create([
            'channel_id' => $channel->id
        ]);

        $threadNotInChannel = $this->thread;

        $this->get('/threads/' . $channel->slug)
            ->assertSee($threadInChannel->title)
            ->assertDontSee($threadNotInChannel->title);
    }

    /** @test */
    public function a_user_can_filter_threads_by_any_username()
    {
         $this->withoutExceptionHandling();

        $this->signIn(User::factory()
            ->create(['name' => 'JohnDoe']));

        $threadByJohn = Thread::factory()
                        ->create(['user_id' => auth()->id()]);

        $threadNotByJohn = $this->thread;

        $this->get('threads?by=JohnDoe')
            ->assertSee($threadByJohn->title)
            ->assertDontSee($threadNotByJohn->title);
    }

    /** @test */
    public function a_user_can_filter_threads_by_popularity()
    {
        $this->withoutExceptionHandling();

        $threadWithTwoReplies = Thread::factory()->create();

        Reply::factory()->count(2)->create([
            'thread_id' => $threadWithTwoReplies->id
        ]);

        $threadWithThreeReplies = Thread::factory()->create();
        Reply::factory()->count(3)->create([
            'thread_id' => $threadWithThreeReplies->id
        ]);

        $threadWithNoReplies = $this->thread;

        $response = $this->getJson('threads?popular=1')->json();

        $this->assertEquals(
            [3, 2, 0],
            array_column($response['data'], 'replies_count')
        );
    }

    /** @test */
    public function a_user_can_filter_threads_by_those_that_are_unanswered()
    {
        $this->withoutExceptionHandling();

        Reply::factory()->create([
            'thread_id' => $this->thread->id
        ]);

        $response = $this->getJson('threads?unanswered=1')->json();

        $this->assertCount(1, $response['data']);
    }

    /** @test */
    public function a_user_can_request_all_replies_for_a_given_thread()
    {
        $this->withoutExceptionHandling();

        $thread = $this->thread;

        Reply::factory()->count(2)->create([
            'thread_id' => $thread->id
        ]);

        $response = $this->getJson($thread->path() . '/replies')->json();

        $this->assertCount(2, $response['data']);
        $this->assertEquals(2, $response['total']);
    }

    /** @test */
    public function we_record_a_new_visit_each_time_the_thread_is_read()
    {
        $thread = $this->thread;

        $this->assertSame(0, $thread->visits);

        $this->call('GET', $thread->path());

        $this->assertEquals(1, $thread->fresh()->visits);
    }
}
