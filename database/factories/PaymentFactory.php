<?php

namespace Database\Factories;

use App\Models\MemberProfile;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
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
            'recorded_by_user_id' => User::factory()->treasurer(),
            'amount' => fake()->randomElement([30, 30, 30, 50, 100]),
            'payment_date' => fake()->dateTimeBetween('-3 months', 'now')->format('Y-m-d'),
            'payment_method' => fake()->randomElement(['cash', 'bank_transfer']),
        ];
    }
}
