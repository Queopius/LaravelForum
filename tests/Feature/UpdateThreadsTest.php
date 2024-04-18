<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\{Thread, User};
use Illuminate\Auth\Access\AuthorizationException;

class UpdateThreadsTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->signIn();
    }

    /** @test */
    public function unauthorized_users_may_not_update_threads()
    {
        $this->expectException(AuthorizationException::class);

        $thread = Thread::factory()->create(['user_id' => $this->createUser()->id]);

        $this->patch($thread->path(), [])->assertStatus(403);
    }

    /** @test */
    public function a_thread_requires_a_title_and_body_to_be_updated()
    {
        //$this->signIn();
        $thread = $this->createTreadRelationUser();

        $this->patch($thread->path(), ['title' => 'Changed'])
            ->assertSessionHasErrors('body');

        $this->patch($thread->path(), ['body' => 'Changed'])
            ->assertSessionHasErrors('title');
    }

    /** @test */
    public function a_thread_can_be_updated_by_its_creator()
    {
        $thread = $this->createTreadRelationUser();

        $this->patch($thread->path(), [
            'title' => 'Changed',
            'body' => 'Changed body.'
        ]);

        tap($thread->fresh(), function ($thread) {
            $this->assertEquals('Changed', $thread->title);
            $this->assertEquals('Changed body.', $thread->body);
        });
    }

    protected function createTreadRelationUser()
    {
        return Thread::factory()
            ->create(
                [
                    'user_id' => User::factory()->create()->id
                ]
            );
    }
}
