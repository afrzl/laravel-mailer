<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Mailer>
 */
class MailerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'mail_body' => $this->faker->randomHtml(),
            'mail_host' => $this->faker->randomElement(['smtp.gmail.com', 'smtp.yahoo.com', 'smtp.outlook.com', 'mail.example.com']),
            'mail_port' => $this->faker->randomElement([587, 465, 25, 2525]),
            'mail_username' => $this->faker->email(),
            'mail_password' => $this->faker->password(),
            'mail_from_address' => $this->faker->email(),
            'mail_from_name' => $this->faker->name(),
            'mail_encryption' => $this->faker->randomElement(['tls', 'ssl', null]),
            'is_active' => $this->faker->boolean(80), // 80% chance of being active
        ];
    }
}
