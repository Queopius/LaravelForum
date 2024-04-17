<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\{RefreshDatabase, WithFaker};

class AddAvatarTest extends TestCase
{
    /** @test */
    public function only_members_can_add_avatars()
    {
        //$this->withoutExceptionHandling();

        $user = $this->createUser();

        $this->json('POST', 'api/users/'.$user->id.'/avatars')
            ->assertStatus(401);

        $this->isAuthenticated('web');
    }

    /** @test */
    public function a_valid_avatar_must_be_provided()
    {
        $this->signIn();

        $this->json('POST', 'api/users/' . auth()->id() . '/avatars', [
            'avatar' => 'not-an-image'
        ])->assertStatus(422);
    }

    /** @test */
    public function a_user_may_add_an_avatar_to_their_profile()
    {
        $this->signIn();

        Storage::fake('public');

        $this->json('POST', 'api/users/' . auth()->id() . '/avatars', [
            'avatar' => $file = UploadedFile::fake()->image('avatars.jpg')
        ]);

        $this->assertEquals(
            asset(Storage::url('avatars/'.$file->hashName())),
            auth()->user()->avatar_path
        );

        Storage::disk('public')->exists('avatars/' . $file->hashName());
    }
}
