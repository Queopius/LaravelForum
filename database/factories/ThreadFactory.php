<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use App\Models\{Channel, Thread, User};
use Illuminate\Database\Eloquent\Factories\Factory;

class ThreadFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Thread::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $title = $this->faker->sentence();

        return [
            'user_id' =>  User::factory()->create()->id,
            'channel_id' => Channel::factory()->create()->id,
            'title' => $title,
            'body'  => $this->faker->paragraph(),
            'visits' => 0,
            'slug' => $title,
            //'slug' => Str::slug($title),
            'locked' => false
        ];
    }
}
