<?php

namespace App\Services;
use App\Models\Message;
use DefStudio\Telegraph\Facades\Telegraph;

class MessageService
{
    public static function saveMessage($message, $user_id)
    {
        Message::create([
            'user_id' => $user_id,
            'message' => $message
        ]);
        Telegraph::message("$message (сообщение было сохранено в бд.)")->send();
    }
}