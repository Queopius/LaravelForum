<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\{Thread, User};
use Illuminate\Foundation\Testing\RefreshDatabase;

class SearchTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_search_threads()
    {
        //$this->signIn(User::factory()->create());
        config(['scout.driver' => 'algolia']);

        Thread::factory()->count(2)->create([]);
        Thread::factory()->count(1)->create([
            'body' => 'A thread with the foobar term.'
        ]);
        Thread::factory()->count(1)->create([
            'body' => 'A thread with the foobar term.'
        ]);
        /* create(Thread::class, [], 2);
        create(Thread::class, ['body' => 'A thread with the foobar term.'], 2); */

        do {
            // Account for latency.
            sleep(.25);

            $results = $this->getJson('/threads/search?q=foobar')->json()['data'];
        } while (empty($results));

        $this->assertCount(2, $results);

        // Clean up.
        Thread::latest()->take(4)->unsearchable();
    }
}
