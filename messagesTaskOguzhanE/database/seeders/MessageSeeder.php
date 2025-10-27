<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Message; 

class MessageSeeder extends Seeder
{
    public function run(): void
    {
        
        Message::truncate(); 

        
        Message::create([
            'recipient' => '+90553443522',
            'content' => 'Bu ilk test mesajıdır.',
            'status' => 'pending'
        ]);

        Message::create([
            'recipient' => '+905554445566',
            'content' => 'Bu ikinci test mesajıdır. Karakter sınırı kontrolü.',
            'status' => 'pending'
        ]);

        
        for ($i = 0; $i < 8; $i++) {
            Message::create([
                'recipient' => '+9055500000' . $i,
                'content' => 'Toplu gönderim testi #' . ($i + 3),
                'status' => 'pending'
            ]);
        }

        
        Message::create([
            'recipient' => '+905559998877',
            'content' => 'Bu zaten gönderilmiş bir mesaj.',
            'status' => 'sent',
            'external_message_id' => 'abc-123-xyz',
            'sent_at' => now()->subDay() 
        ]);
    }
}