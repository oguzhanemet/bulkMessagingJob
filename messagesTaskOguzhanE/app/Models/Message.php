<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    
    protected $fillable = [ 
        'recipient', 
        'content', 
        'status', 
        'external_message_id', 
        'sent_at'
    ];

    
    protected $casts = [
        'sent_at' => 'datetime',
    ];
}
