<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Message;

class MessageApiTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_list_only_sent_messages_and_uses_correct_json_format()
    {

        $sentMessage = Message::factory()->create([
            'status' => 'sent', 
            'external_message_id' => 'api-test-uuid',
            'content' => 'Test Icerik',
            'sent_at' => now(),
        ]);
        
        Message::factory()->create(['status' => 'pending']);

        $response = $this->getJson('/api/messages');
      
        $response->assertStatus(200)
                 ->assertJsonCount(1) 
                 ->assertJsonStructure([
                     '*' => ['id', 'recipient', 'content', 'messageId', 'sent_at'] 
                 ])
                 ->assertJsonFragment([
                     
                     'content' => 'Test Icerik',
                     'messageId' => 'api-test-uuid'
                 ]);
        
        
        $response->assertJsonMissing(['id' => $sentMessage->id + 1]);
    }
}