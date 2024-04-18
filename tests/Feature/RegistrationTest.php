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
    public function a_confirmation_email_is_sent_upon_registration()
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
    public function user_can_fully_confirm_their_email_addresses()
    {
        $user = User::factory()->make();

        $userData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password123'),
        ];

        //dd($user->password);

        Mail::fake();
        Mail::assertNothingSent();

        //dd($user->toArray());
        //$this->post(route('register'), $user->toArray());
        $response = $this->post(route('register'), $userData);
        dd($response->content());

        $user = User::firstWhere('email', $user->email);

        //Mail::hasSent($user, PleaseConfirmYourEmail::class);
        Mail::assertSentTimes(1, PleaseConfirmYourEmail::class);

        tap($user->fresh(), function ($user) {
            $this->assertTrue($user->confirmed);
            $this->assertNull($user->confirmation_token);
        });
    }

    // /** @test */
    // function confirming_an_invalid_token()
    // {
    //     $this->get(route('register.confirm', ['token' => 'invalid']))
    //         ->assertRedirect(route('threads'))
    //         ->assertSessionHas('flash', 'Unknown token.');
    // }
}
