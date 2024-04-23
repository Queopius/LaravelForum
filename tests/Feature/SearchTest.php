<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Thread;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SearchTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_search_threads()
    {
        config(['scout.driver' => 'algolia']);

        $search = 'foobar';

        Thread::factory()->count(2)->create([]);
        Thread::factory()->count(2)->create([
            'body' => 'A thread with the {$search} term.'
        ]);

        do {
            // Account for latency.
            sleep(.20);

            $results = $this->getJson('/threads/search?q={$search}')->json()['data'];
        } while (empty($results));

        $this->assertCount(2, $results);

        // Clean up.
        Thread::latest()->take(4)->unsearchable();
    }
}
