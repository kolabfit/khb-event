<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ticket;
use App\Models\Payment;
use App\Models\Event;
use App\Models\User;
use App\Models\TicketType;
use Faker\Factory as Faker;

class TicketAndPaymentSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        // Pastikan ada minimal 1 Event, 1 User, 1 TicketType
        $eventCount      = Event::count();
        $userCount       = User::count();
        $ticketTypeCount = TicketType::count();

        if ($eventCount === 0 || $userCount === 0 || $ticketTypeCount === 0) {
            $this->command->info('[Seed] Tambahkan dulu data Event, User, dan TicketType.');
            return;
        }

        $tickets = [];

        // 1) Create 10 Ticket
        for ($i = 0; $i < 10; $i++) {
            // Ambil TicketType acak beserta harganya
            $tt = TicketType::inRandomOrder()->first();

            $ticket = Ticket::create([
                'event_id'       => Event::inRandomOrder()->first()->id,
                'user_id'        => User::inRandomOrder()->first()->id,
                'ticket_type_id' => $tt->id,
                'status'         => $faker->randomElement(['pending', 'paid']),
                'price_paid'     => $tt->price,                        // ← pastikan terisi
                'created_at'     => $faker->dateTimeBetween('-1 month', 'now'),
                'updated_at'     => now(),
            ]);

            $tickets[] = $ticket;
        }

        // 2) Create 10 Payment, 1 per Ticket yang baru dibuat
        foreach ($tickets as $ticket) {
            Payment::create([
                'ticket_id'  => $ticket->id,
                'amount'     => $ticket->price_paid,                   // atau pakai nilai sendiri
                'method'     => $faker->randomElement(['midtrans', 'xendit', 'manual']),
                'status'     => $ticket->status === 'paid'
                                ? 'paid'
                                : $faker->randomElement(['pending', 'failed']),
                'transaction_id' => $faker->ean13(),
                'created_at' => $ticket->created_at,
                'updated_at' => now(),
            ]);
        }

        $this->command->info('[Seed] 10 Ticket & 10 Payment berhasil dibuat.');
    }
}
