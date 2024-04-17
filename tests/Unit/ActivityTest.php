<?php

namespace Tests\Unit;

use Carbon\Carbon;
use Tests\TestCase;
use App\Models\User;
use App\Models\{Activity, Reply, Thread};
use Illuminate\Foundation\Testing\RefreshDatabase;

class ActivityTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_records_activity_when_a_thread_is_created()
    {
        $this->signIn();
        $thread = Thread::factory()->count(1)->create();

        $this->assertDatabaseHas('activities', [
            'type' => 'created_thread',
            'user_id' => auth()->id(),
            'subject_id' => $thread->first()->id,
            'subject_type' => Thread::class
        ]);

        $activity = Activity::first();

        $this->assertEquals($activity->subject->id, $thread->first()->id);
        $this->assertAuthenticated('web');
    }

    /** @test */
    function it_records_activity_when_a_reply_is_created()
    {
        $this->signIn();

        Reply::factory()->create();

        $this->assertEquals(2, Activity::count());
    }

    /** @test */
    function it_fetches_a_feed_for_any_user()
    {
        $this->signIn();

        Thread::factory()->count(4)->create(['user_id' => auth()->id()]);

        $user = User::factory()->create();

        if (auth()->check()) {
            $user->first()->activity()->update([
                'created_at' => Carbon::now()->subWeek()
            ]);
        }

        $feed = Activity::feed(auth()->user(), 50);

        /* dd($feed->keys()->contains(
            Carbon::now()->format('Y-m-d')));

        $this->assertTrue($feed->keys()->contains(
            Carbon::now()->format('Y-m-d')
        )); */

        $this->assertTrue($feed->keys()->contains(
            Carbon::now()->subWeek()->format('Y-m-d')
        ));
    }
}
