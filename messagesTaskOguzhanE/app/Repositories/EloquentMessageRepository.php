<?php
namespace App\Repositories;

use App\Models\Message;
use Illuminate\Support\Facades\DB; 
use Carbon\Carbon;

class EloquentMessageRepository implements MessageRepositoryInterface
{
    public function getPendingMessages(int $limit)
    {

        return Message::where('status', 'pending')->limit($limit)->get();
    }

    public function markAsSent(int $messageId, string $externalId)
    {
        return Message::where('id', $messageId)->update([
            'status' => 'sent',
            'external_message_id' => $externalId,
            'sent_at' => Carbon::now()
        ]);
    }

    public function markAsFailed(int $messageId)
    {
        return Message::where('id', $messageId)->update(['status' => 'failed']);
    }

    public function getSentMessages()
    {
        
        return Message::where('status', 'sent')
                    ->select('id', 'recipient', 'content', 'external_message_id', 'sent_at')
                    ->get();
    }
}