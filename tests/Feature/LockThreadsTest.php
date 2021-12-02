<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\{User, Thread};
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LockThreadsTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    function non_administrators_may_not_lock_threads()
    {
        $this->signIn();

        $thread = Thread::factory()->create(['user_id' => auth()->id()]);

        $this->post(route('locked-threads.store', $thread))->assertStatus(403);

        $this->assertFalse($thread->fresh()->locked);
    } 

    /** @test */
    function administrators_can_lock_threads()
    {
        $this->withoutExceptionHandling();
        $this->signIn(User::factory()->states('administrator')->create());

        $thread = Thread::factory()->create(['user_id' => auth()->id()]);

        $this->post(route('locked-threads.store', $thread));

        $this->assertTrue($thread->fresh()->locked, 'Failed asserting that the thread was locked.');
    }

    /** @test */
    function administrators_can_unlock_threads()
    {
        $this->withoutExceptionHandling();
        $this->signIn(User::factory()->states('administrator')->create());

        $thread = Thread::factory()->create(['user_id' => auth()->id(), 'locked' => true]);

        $this->delete(route('locked-threads.destroy', $thread));

        $this->assertFalse($thread->fresh()->locked, 'Failed asserting that the thread was unlocked.');
    }

    /** @test */
    public function once_locked_a_thread_may_not_receive_new_replies()
    {
        $this->signIn();

        $thread =Thread::factory()->create(['locked' => true]);

        $this->post($thread->path() . '/replies', [
            'body' => 'Foobar',
            'user_id' => auth()->id()
        ])->assertStatus(422);
    }
}
