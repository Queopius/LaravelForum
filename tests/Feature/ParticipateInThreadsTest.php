<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\{Reply, Thread};
use App\Exceptions\ThrottleException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ParticipateInThreadsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
   public function guest_cant_add_reply()
    {
        $this->signIn();

        /* $thread = Thread::factory()->create();
        dd(Thread::factory()->create()->path()); */
        $this->post(Thread::factory()->create()->path() . '/replies', [
            'body' => 'I am an text body',
        ])->assertRedirect();

        //->assertRedirect(route('login'));
    }

    /** @test */
   public function an_authenticated_user_may_participate_in_forum_threads()
    {
        $this->signIn();

        $thread = Thread::factory()->create();
        $reply = Reply::factory()->make();

        $this->post($thread->path() . '/replies', $reply->toArray());

        $this->assertDatabaseHas('replies', $reply->only('body'));
        $this->assertEquals(1, $thread->fresh()->replies_count);
    }

    /** @test */
   public function unauthorized_users_cannot_delete_replies()
    {
        $this->expectException(AuthenticationException::class);
        $reply = Reply::factory()->create();

        $this->delete("replies/{$reply->id}")
            ->assertRedirect('login');

        $this->signIn()->delete("replies/{$reply->id}")
            ->assertStatus(403);
    }

    /** @test */
   public function authorized_users_can_delete_replies()
    {
        $this->signIn();
        $reply = Reply::factory()->create([
            'user_id' => auth()->id()
        ]);

        $this->delete("replies/{$reply->id}");

        $this->assertDatabaseMissing('replies', $reply->only('id'));
    }

    public function unauthorized_users_cannot_update_replies()
    {
        $reply = Reply::factory()->create();

        $this->patch("/replies/{$reply->id}")
            ->assertRedirect('login');

        $this->signIn()
            ->patch("/replies/{$reply->id}")
            ->assertStatus(403);
    }

    /** @test */
   public function authorized_users_can_update_replies()
    {
        $this->signIn();

        $reply = Reply::factory()->create([
            'user_id' => auth()->id()
        ]);

        $updatedReply = 'changed';
        $this->patch("/replies/{$reply->id}", ['body' => $updatedReply]);

        $this->assertDatabaseHas('replies', ['id' => $reply->id, 'body' => $updatedReply]);
    }

    /** @test */
   public function replies_that_contain_spam_may_not_be_created()
    {
        $this->signIn();

        $thread = Thread::factory()->create();
        $reply = Reply::factory()->make([
            'body' => 'Yahoo Customer Support'
        ]);

        $this->expectException(ValidationException::class);

        $this->json('post', $thread->path() . '/replies', $reply->toArray())
            ->assertStatus(422);
    }

    /** @test */
   public function users_may_only_reply_a_maximum_of_once_per_minute()
    {
        $this->signIn();

        $thread = Thread::factory()->create();
        $reply = Reply::factory()->make();

        $this->post($thread->path() . '/replies', $reply->toArray())
            ->assertStatus(201);

        $this->expectException(ThrottleException::class);

        $this->post($thread->path() . '/replies', $reply->toArray())
            ->assertStatus(429);
    }
}
