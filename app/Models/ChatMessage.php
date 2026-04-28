<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatMessage extends Model
{
    protected $fillable = [
        'user_id',
        'user_name',
        'message',
        'attachment_path',
        'attachment_original_name',
        'attachment_mime',
        'attachment_size',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

