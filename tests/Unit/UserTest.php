<?php
 
namespace Tests\Unit;

use Tests\TestCase;
use App\Models\{User, Reply};
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test */
    public function a_user_can_fetch_their_most_recent_reply()
    {
        $this->withoutExceptionHandling();

        $user = create(User::class);

        $reply = create(Reply::class, ['user_id' => $user->id]);

        $this->assertEquals($reply->id, $user->lastReply->id);
    }

    /** @test */
    function a_user_can_determine_their_avatar_path()
    {
        $this->withoutExceptionHandling();
        
        $user = create(User::class);

        $this->assertEquals(asset('images/avatars/default.png'), $user->avatar_path);

        $user->avatar_path = 'avatars/me.jpg';

        $this->assertEquals(asset('avatars/me.jpg'), $user->avatar_path);
    }
}
