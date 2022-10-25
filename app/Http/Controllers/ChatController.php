<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    public function index()
    {
        $chats = Auth::user()->chats()->with('messages', 'users')->get();
        return view("chat.chat", compact('chats'));
    }

    public function chats()
    {
        $chats = Auth::user()->chats()->select('chats.id', 'is_group', 'name')->with('messages', 'users')->get();
        $chatLists = [];

        foreach($chats as $chat) {
            $temp = $chat->toArray();
            $temp['user'] = $chat->display()->toArray();
            $chatLists[] = $temp;
        }
        
        return response()->json([ 'data' => $chatLists ]);
    }

    public function chat(Request $request, $chatId)
    {
        $chats = Auth::user()->chats()->with('messages', 'users')->get();
        $chat = Auth::user()->chats()->where('chats.id', $chatId)->with('messages.senderData')->first();
        return view("chat.chat", compact('chats', 'chat'));
    }

    public function create(Request $request)
    {
        $userId = Auth::id();

        $request->validate([
            'email' => 'required|email|exists:users,email',
        ],[
            'exists' => "Email not found"
        ]);

        $targetedUser = User::where('email', $request->email)->first();

        if (!$targetedUser->hasChats()) 
        {
            $chatCreated = Chat::create([
                'name' => NULL,
                'is_group' => false,
                'created_by' => $userId
            ]);
            $chatCreated->users()->sync([$userId, $targetedUser->id]);
        }

        $concat_users = [];
        array_push($concat_users, $targetedUser->id, $userId);
        sort($concat_users); //ASC SORT
        $concat_users = implode(",", $concat_users); // ARRAY TO STRING

        $chat = DB::table('chat_user')
                ->select(
                    DB::raw('chats.id chat_id'),
                    DB::raw('GROUP_CONCAT(users.id ORDER BY users.id ASC) as user_ids'),
                    DB::raw('GROUP_CONCAT(users.name ORDER BY users.id ASC) as user_names'),
                )
                ->join('chats', 'chat_user.chat_id','=','chats.id')
                ->join('users', 'chat_user.user_id','=','users.id')
                ->groupBy('chats.id')
                ->having('user_ids', '=', $concat_users)
                ->first();

        return response()->json(['data' => $chat ]);
    }

    public function messages(Request $request)
    {
        $chatId = $request->chatId;

        $messages = Message::whereHas('chat', function($q) use($chatId) {
            $q->where('id', $chatId);
            $q->whereHas('users', function ($q){
                $q->where('users.id', Auth::id());
            });
        })->with('senderData')->get();

        return response()->json([ 'data' => $messages ]);
    }

    public function addMessage(Request $request)
    {
        try {
            $chat = Auth::user()->chats()->where('chats.id', $request->chat_id)->first();

            $data = [
                'chat_id' => $request->chat_id,
                'sender' => Auth::id(),
                'message' => $request->message,
            ];

            $chat->messages()->create($data);
            event( new \App\Events\MessagesEvent($data) );    
            return response()->json($chat);

        } catch (\Throwable $th) {
            return response()->json($th->getMessage(), 500);
        }
    }
}
