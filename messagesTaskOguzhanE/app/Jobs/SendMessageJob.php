<?php

namespace App\Jobs;

use App\Models\Message;
use App\Repositories\MessageRepositoryInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\Middleware\RateLimited; 
use Illuminate\Support\Facades\Http;         
use Illuminate\Support\Facades\Log;          
use Illuminate\Support\Facades\Cache;       
use Carbon\Carbon;          
use Illuminate\Support\Str;                 

class SendMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $message;

    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    
    public function middleware()
    {
        
        return [new RateLimited('sms-gateway')]; 
    }

    /**
     * Job'ı çalıştır.
     */
    public function handle(MessageRepositoryInterface $repository): void
{

   
    
    $externalId = (string) Str::uuid(); 

    
    $contentWithId = $this->message->content . ' [ID: ' . $externalId . ']';

    try {
        
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'x-ins-auth-key' => env('WEBHOOK_API_KEY')
        ])->post(env('WEBHOOK_SITE_URL'), [
            'to' => $this->message->recipient,
            'content' => $contentWithId 
        ]);

       
        if ($response->successful() && $response->status() == 202) {

            
            $repository->markAsSent($this->message->id, $externalId);

            
            $sentAt = Carbon::now(); 
            $cacheKey = 'message:' . $externalId; 
            
            Cache::store('redis')->put($cacheKey, [
                'internal_message_id' => $this->message->id,
                'sent_at' => $sentAt->toIso8601String(),
                'status' => 'Accepted' 
            ], now()->addDay()); 

        
            } else {
                
                Log::error('Webhook API failed for message: ' . $this->message->id, [
                    'status' => $response->status(), 
                    'body' => $response->body()
                ]);
                $repository->markAsFailed($this->message->id);
            }

        } catch (\Exception $e) {
            
            Log::critical('Job failed entirely for message: ' . $this->message->id, [
                'error' => $e->getMessage()
            ]);
            $repository->markAsFailed($this->message->id);
            
        }
    }
}