<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BookingAndOpeningSeeder extends Seeder
{
    public function run(): void
    {
        // booking_settings base
        DB::table('booking_settings')->truncate();
        DB::table('booking_settings')->insert([
            'booking_start_time' => '08:00:00',
            'booking_end_time'   => '20:00:00',
            'min_days_advance'   => 1,
        ]);

        // opening_hours secondo le regole richieste
        DB::table('opening_hours')->truncate();

        // mappa: 0=Lun … 6=Dom
        $config = [
            0 => [ ['10:00','13:00'], ['16:30','19:30'] ], // Lun
            1 => [ ['10:00','13:00'], ['16:30','19:30'] ], // Mar
            2 => [ ['10:00','13:00'], ['16:30','19:30'] ], // Mer
            3 => [ ['10:00','13:00'] ],                    // Gio (solo mattina)
            4 => [ ['10:00','13:00'], ['16:30','19:30'] ], // Ven
            5 => [ ['10:00','13:00'], ['16:30','19:30'] ], // Sab
            6 => [ /* Domenica: chiuso */ ],               // Dom
        ];

        foreach ($config as $dow => $ranges) {
            foreach ($ranges as $range) {
                DB::table('opening_hours')->insert([
                    'day_of_week'  => $dow,
                    'opening_time' => $range[0],
                    'closing_time' => $range[1], // inclusivo (13:00 e 19:30 devono comparire)
                    'is_active'    => true,
                ]);
            }
        }
    }
}
