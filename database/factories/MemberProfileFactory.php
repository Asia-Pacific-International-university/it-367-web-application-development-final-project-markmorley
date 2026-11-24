<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\MemberProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MemberProfile>
 */
class MemberProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'full_name' => fake()->name(),
            'student_id' => fake()->unique()->numerify('S#####'),
            'phone' => fake()->optional()->numerify('08########'),
        ];
    }
}
