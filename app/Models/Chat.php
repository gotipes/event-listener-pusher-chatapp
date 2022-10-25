<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Chat extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'is_group', 'created_by'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'chat_user', 'chat_id', 'user_id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function display()
    {
        if (!$this->is_group) {
            return $this->users()->where('users.id', '!=' ,Auth::id())->first();
        }

        return $this;
    }
}
