<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\{User, Thread};

class UpdateThreadsTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->signIn();
    }

    /** @test */
    function unauthorized_users_may_not_update_threads()
    {
        $thread = Thread::factory()->create(['user_id' => $this->createUser()->id]);

        $this->patch($thread->path(), [])->assertStatus(403);
    }

    /** @test */
    function a_thread_requires_a_title_and_body_to_be_updated()
    {
        $thread = $this->createTreadRelationUser();

        $this->patch($thread->path(), ['title' => 'Changed'])->assertSessionHasErrors('body');

        $this->patch($thread->path(), ['body' => 'Changed'])->assertSessionHasErrors('title');
    }
    
    /** @test */
    function a_thread_can_be_updated_by_its_creator()
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
        return Thread::factory()->create(['user_id' => $this->createUser()->id]);
    }
}
