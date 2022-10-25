<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = ['chat_id', 'sender', 'message', 'created_at'];
    // protected $attributes = ['unix_created_at']; //Add extra attribute (error when create message)
    protected $appends  = ['unix_created_at']; //Make it available in the json response

    public function senderData()
    {
        return $this->belongsTo(User::class, 'sender', 'id');
    }

    public function chat()
    {
        return $this->belongsTo(Chat::class);
    }

    public function getUnixCreatedAtAttribute()
    {
        return strtotime($this->created_at);
    }
}
