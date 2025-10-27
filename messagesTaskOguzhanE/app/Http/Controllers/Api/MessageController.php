<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\MessageService;
use App\Models\Message; 
use App\Jobs\SendMessageJob; 
use Illuminate\Support\Facades\Validator; 
use Illuminate\Support\Facades\Http;

/**
 * @OA\Info(
 * version="1.0.0",
 * title="Mesaj Gönderme Servisi API Dokümantasyonu",
 * description="Mesaj kuyruklama, gönderme ve takip işlemlerini gerçekleştirir.",
 * @OA\Contact(
 * email="ornek@example.com"
 * ),
 * )
 * * @OA\Tag(
 * name="Messages",
 * description="Mesaj Gönderme ve Takip İşlemleri"
 * )
 */

class MessageController extends Controller
{
    protected $messageService;

    
    public function __construct(MessageService $messageService)
    {
        $this->messageService = $messageService;
    }

    /**
     * @OA\Post(
     * path="/api/messages",
     * summary="Mesaj gönderme isteği başlatır.",
     * tags={"Messages"},
     * @OA\RequestBody(
     * required=true,
     * description="Gönderilecek mesaj verileri",
     * @OA\JsonContent(
     * required={"to", "content"},
     * @OA\Property(property="to", type="string", example="+905551112233", description="Mesajın gönderileceği alıcı numarası"),
     * @OA\Property(property="content", type="string", example="Merhaba, bu bir deneme mesajıdır.", description="Mesaj içeriği")
     * )
     * ),
     * @OA\Response(
     * response=202,
     * description="Mesaj kuyruğa atılarak işlenmek üzere kabul edildi.",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Accepted for processing"),
     * @OA\Property(property="messageId", type="integer", example="101")
     * )
     * ),
     * @OA\Response(
     * response=422,
     * description="Doğrulama hatası (Validation Error)."
     * )
     * )
     */
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'to' => 'required|string|max:20',
            'content' => 'required|string|max:160',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        
        $message = Message::create([
            'recipient' => $request->input('to'),
            'content' => $request->input('content'),
            'status' => 'pending', 
        ]);

        SendMessageJob::dispatch($message);

        return response()->json([
            'message' => 'Accepted for processing',
            'messageId' => (string) $message->id,
        ], 202);
    }

    /**
     * @OA\Get(
     * path="/api/messages",
     * summary="Gönderilmiş mesajları listeler.",
     * tags={"Messages"},
     * @OA\Response(
     * response=200,
     * description="Gönderilmiş mesajların listesi",
     * @OA\JsonContent(type="array", @OA\Items(
     * @OA\Property(property="id", type="integer"),
     * @OA\Property(property="recipient", type="string"),
     * @OA\Property(property="content", type="string"),
     * @OA\Property(property="messageId", type="string", description="Harici SMS sisteminden dönen ID"),
     * @OA\Property(property="sent_at", type="string", format="date-time")
     * ))
     * )
     * )
     */
    public function index()
    {
        $messages = $this->messageService->getSentMessageList();

        return response()->json($messages);
    }
}