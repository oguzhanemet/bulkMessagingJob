<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\MessageService; 

class DispatchPendingMessagesCommand extends Command
{
    
    protected $signature = 'messages:dispatch';
    protected $description = 'Dispatch all pending messages to the queue';

    public function handle(MessageService $messageService)
    {
        $this->info('Finding pending messages and dispatching to queue...');

        
        $count = $messageService->dispatchPendingMessages();

        $this->info($count . ' messages have been dispatched to the queue.');
        $this->info('Run "php artisan queue:work" to start processing.');
        return 0;
    }
}