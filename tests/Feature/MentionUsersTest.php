<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\{Reply, Thread, User};

class MentionUsersTest extends TestCase
{
    /** @test */
    public function mentioned_users_in_a_reply_are_notified()
    {
        $this->withoutExceptionHandling();
        $john = $this->createUser(['name' => 'JohnDoe']);

        $this->signIn($john);

        $jane = $this->createUser(['name' => 'JaneDoe']);

        $thread = Thread::factory()->create();

        $reply = Reply::factory()->create([
            'body' => 'Hey @JaneDoe check this out.'
        ]);

        $this->json('post', $thread->path() . '/replies', $reply->toArray());

        $this->assertCount(1, $jane->notifications);
    }

    /** @test */
    public function it_can_fetch_all_mentioned_users_starting_with_the_given_characters()
    {
        $this->createUser(['name' => 'JohnDoe']);
        $this->createUser(['name' => 'johndoe2']);
        $this->createUser(['name' => 'JaneDoe']);

        $results = $this->json('GET', '/api/users', ['name' => 'john']);

        $this->assertCount(2, $results->json());
    }
}
