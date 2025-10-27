<?php
namespace App\Services;

use App\Repositories\MessageRepositoryInterface;
use App\Jobs\SendMessageJob; 

class MessageService
{
    protected $messageRepository;

    public function __construct(MessageRepositoryInterface $messageRepository)
    {
        $this->messageRepository = $messageRepository;
    }

    public function dispatchPendingMessages()
    {
        
        $messages = $this->messageRepository->getPendingMessages(100); 

        foreach ($messages as $message) {
            
            SendMessageJob::dispatch($message);
        }
        return $messages->count();
    }

    public function getSentMessageList()
    {
        
    $sentMessages = $this->messageRepository->getSentMessages(); 

    return $sentMessages->map(function ($message) {
        return [
            'id' => $message->id,
            'recipient' => $message->recipient,
            'content' => $message->content, 
            'messageId' => $message->external_message_id, 
            'sent_at' => $message->sent_at,
        ];
    });
    }
}