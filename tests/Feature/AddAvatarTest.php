<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AddAvatarTest extends TestCase
{
    /** @test */
    function only_members_can_add_avatars()
    {      
        //$this->withoutExceptionHandling();
        
        $user = $this->createUser();

        $this->json('POST', 'api/users/'.$user->id.'/avatars')
            ->assertStatus(401);

        $this->isAuthenticated('web');
    }

    /** @test */
    function a_valid_avatar_must_be_provided()
    {
        $this->signIn();

        $this->json('POST', 'api/users/' . auth()->id() . '/avatars', [
            'avatar' => 'not-an-image'
        ])->assertStatus(422);
    }

    /** @test */
    function a_user_may_add_an_avatar_to_their_profile()
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
