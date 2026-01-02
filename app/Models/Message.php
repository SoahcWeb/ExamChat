<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Conversation;

class Message extends Model
{
    use HasFactory;

    /**
     * Les champs que l'on peut remplir en masse
     */
    protected $fillable = [
        'conversation_id',
        'role',        // 'user' ou 'assistant'
        'content',
    ];

    /**
     * Un message appartient à une conversation
     */
    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }
}
