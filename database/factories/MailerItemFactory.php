<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MailerItem>
 */
class MailerItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = $this->faker->randomElement(['pending', 'sent', 'failed', 'delivered', 'bounced']);
        $sentAt = $status !== 'pending' ? $this->faker->dateTimeBetween('-1 month', 'now') : null;
        $deliveredAt = $status === 'delivered' ? $this->faker->dateTimeBetween($sentAt, 'now') : null;
        
        return [
            'mailer_id' => \App\Models\Mailer::factory(),
            'recipient_email' => $this->faker->email(),
            'recipient_name' => $this->faker->name(),
            'status' => $status,
            'sent_at' => $sentAt,
            'delivered_at' => $deliveredAt,
            'error_message' => $status === 'failed' || $status === 'bounced' ? $this->faker->sentence() : null,
            'metadata' => [
                'ip_address' => $this->faker->ipv4(),
                'user_agent' => $this->faker->userAgent(),
                'tracking_id' => $this->faker->uuid(),
            ],
        ];
    }

    /**
     * Indicate that the mailer item is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'sent_at' => null,
            'delivered_at' => null,
            'error_message' => null,
        ]);
    }

    /**
     * Indicate that the mailer item is sent.
     */
    public function sent(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'sent',
            'sent_at' => $this->faker->dateTimeBetween('-1 week', 'now'),
            'delivered_at' => null,
            'error_message' => null,
        ]);
    }

    /**
     * Indicate that the mailer item failed.
     */
    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'failed',
            'sent_at' => null,
            'delivered_at' => null,
            'error_message' => $this->faker->sentence(),
        ]);
    }
}
