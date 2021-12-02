<?php

namespace Database\Factories;

use Ramsey\Uuid\Uuid;
use App\Notifications\ThreadWasUpdated;
use App\Models\{User, DatabaseNotificationUser};
use Illuminate\Database\Eloquent\Factories\Factory;

class DatabaseNotificationUserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = DatabaseNotificationUser::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'id' => Uuid::uuid4()->toString(),
            'type' => ThreadWasUpdated::class,
            'notifiable_id' => function () {
                return auth()->id() ?: User::factory()->create()->id;
            },
            'notifiable_type' => User::class,
            'data' => ['foo' => 'bar']
        ];
    }
}
