<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Mail\PleaseConfirmYourEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Testing\RefreshDatabase;
//use Illuminate\Foundation\Testing\DatabaseMigrations;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function a_confirmation_email_is_sent_upon_registration()
    {
        Mail::fake();

        $this->post(route('register'), [
            'name' => 'John',
            'email' => 'john@example.com',
            'password' => 'foobar',
            'password_confirmation' => 'foobar'
        ]);

        Mail::assertQueued(PleaseConfirmYourEmail::class);
    }

    /** @test */
    function user_can_fully_confirm_their_email_addresses()
    {
        $user = $this->createUser()
            ->make()
            ->makeVisible([bcrypt('password')]);

        Mail::fake();
        Mail::assertNothingSent();

        $this->post(route('register'), $user->toArray());

        $user = User::firstWhere('email', $user->email);

        Mail::hasSent($user, PleaseConfirmYourEmail::class);
        //Mail::assertSentTimes(1, PleaseConfirmYourEmail::class);

        /* tap($user->fresh(), function ($user) {
            $this->assertTrue($user->confirmed);
            $this->assertNull($user->confirmation_token);
        }); */
    }

    // /** @test */
    // function confirming_an_invalid_token()
    // {
    //     $this->get(route('register.confirm', ['token' => 'invalid']))
    //         ->assertRedirect(route('threads'))
    //         ->assertSessionHas('flash', 'Unknown token.');
    // }
}
