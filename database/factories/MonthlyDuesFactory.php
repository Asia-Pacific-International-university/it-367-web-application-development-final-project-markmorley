<?php

namespace Database\Factories;

use App\Models\MemberProfile;
use App\Models\MonthlyDues;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MonthlyDues>
 */
class MonthlyDuesFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'member_profile_id' => MemberProfile::factory(),
            'month' => fake()->numberBetween(1, 12),
            'year' => now()->year,
            'required_amount' => 30.00,
        ];
    }
}
