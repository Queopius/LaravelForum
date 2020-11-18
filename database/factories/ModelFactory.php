<?php

use Illuminate\Support\Str;
use App\Notifications\ThreadWasUpdated;
use App\Models\{User, Channel, Thread, Reply};

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(User::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'email_verified_at' => now(),
        'password' => $password ?: $password = 'secret',
        'remember_token' => Str::random(10),
        // 'confirmed' => true
    ];
});


// $factory->state(User::class, 'unconfirmed', function () {
//     return [
//         'confirmed' => false
//     ];
// });

$factory->state(User::class, 'administrator', function () {
    return [
        'name' => 'JohnDoe'
    ];
});


$factory->define(Thread::class, function ($faker) {
    $title = $faker->sentence;

    return [
        'user_id' => function () {
            return factory(User::class)->create()->id;
        },
        'channel_id' => function () {
            return factory(Channel::class)->create()->id;
        },
        'title' => $title,
        'body'  => $faker->paragraph,
        'visits' => 0,
        'slug' => Str::slug($title),
        'locked' => false
    ];
});

$factory->define(Channel::class, function ($faker) {
    $name = $faker->word;

    return [
        'name' => $name,
        'slug' => $name
    ];
});


$factory->define(Reply::class, function ($faker) {
    return [
        'thread_id' => function () {
            return factory(Thread::class)->create()->id;
        },
        'user_id' => function () {
            return factory(User::class)->create()->id;
        },
        'body'  => $faker->paragraph
    ];
});

$factory->define(\Illuminate\Notifications\DatabaseNotification::class, function ($faker) {
    return [
        'id' => \Ramsey\Uuid\Uuid::uuid4()->toString(),
        'type' => ThreadWasUpdated::class,
        'notifiable_id' => function () {
            return auth()->id() ?: factory(User::class)->create()->id;
        },
        'notifiable_type' => User::class,
        'data' => ['foo' => 'bar']
    ];
});
