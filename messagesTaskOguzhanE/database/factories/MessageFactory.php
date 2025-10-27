<?php
// database/factories/MessageFactory.php

namespace Database\Factories;

use App\Models\Message;
use Illuminate\Database\Eloquent\Factories\Factory;

class MessageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Message::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array // <-- BU METODU EKLİYORUZ!
    {
        return [
            // Sahte telefon numarası (uluslararası formatta)
            'recipient' => '+905' . $this->faker->numerify('########'), 
            
            // Sahte mesaj içeriği
            'content' => $this->faker->sentence(10),
            
            // Varsayılan durumu pending olarak ayarla
            'status' => 'pending', 
            
            // Diğer alanları (external_message_id, sent_at) boş (null) bırakabiliriz,
            // çünkü bunlar Job çalıştıktan sonra dolduruluyor.
        ];
    }
}