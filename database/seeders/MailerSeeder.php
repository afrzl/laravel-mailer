<?php

namespace Database\Seeders;

use App\Models\Mailer;
use App\Models\MailerItem;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MailerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create some users if they don't exist
        $users = User::factory(3)->create();

        // Create mailers for each user
        $users->each(function ($user) {
            $mailers = Mailer::factory(2)->create([
                'user_id' => $user->id,
            ]);

            // Create mailer items for each mailer
            $mailers->each(function ($mailer) {
                // Create some pending items
                MailerItem::factory(5)->pending()->create([
                    'mailer_id' => $mailer->id,
                ]);

                // Create some sent items
                MailerItem::factory(3)->sent()->create([
                    'mailer_id' => $mailer->id,
                ]);

                // Create some failed items
                MailerItem::factory(2)->failed()->create([
                    'mailer_id' => $mailer->id,
                ]);
            });
        });
    }
}
