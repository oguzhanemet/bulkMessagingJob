<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Message;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Repositories\EloquentMessageRepository;

class MessageRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new EloquentMessageRepository();
    }

    /** @test */
    public function it_marks_a_message_as_sent_and_records_external_id()
    {

        $message = Message::factory()->create(['status' => 'pending']);
        $externalId = 'test-external-id-123';

        $this->repository->markAsSent($message->id, $externalId);

        $this->assertDatabaseHas('messages', [
            'id' => $message->id,
            'status' => 'sent',
            'external_message_id' => $externalId,
        ]);
        
        $this->assertNotNull($message->fresh()->sent_at);
    }

    /** @test */
    public function it_only_retrieves_pending_messages()
    {
        Message::factory()->count(3)->create(['status' => 'pending']);
        Message::factory()->count(2)->create(['status' => 'sent']);

        $pendingMessages = $this->repository->getPendingMessages(10);

        $this->assertCount(3, $pendingMessages);
        $pendingMessages->each(function ($message) {
            $this->assertEquals('pending', $message->status);
        });
    }
}