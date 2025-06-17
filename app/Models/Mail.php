<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mail extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'subject',
        'sender',
        'recipient',
        'body',
        'status', // 'draft', 'sent', 'read'
        'sent_at',
    ];
}
