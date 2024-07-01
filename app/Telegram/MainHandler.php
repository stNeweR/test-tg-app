<?php

namespace App\Telegram;

use App\Services\MessageService;
use App\Services\UserService;
use DefStudio\Telegraph\Facades\Telegraph;
use DefStudio\Telegraph\Handlers\WebhookHandler;
use DefStudio\Telegraph\Keyboard\Button;
use DefStudio\Telegraph\Keyboard\Keyboard;
use DefStudio\Telegraph\Keyboard\ReplyButton;
use DefStudio\Telegraph\Keyboard\ReplyKeyboard;
use Illuminate\Http\Request;
use Illuminate\Support\Stringable;
use Log;

class MainHandler extends WebhookHandler
{
    public $userService;

    public function __construct()
    {
        $this->userService = new UserService();
    }

    public function start()
    {
        $this->chat->message('Здравствуйте, чтобы начать пользоваться ботом нужно авторизоваться. После того как вы отпарвите мне свой номер телефона, вам придет смс с кодом.')->replyKeyboard(ReplyKeyboard::make()->oneTime()->buttons([
           ReplyButton::make('Отправить свой номер телефона')->requestContact()
       ]))->send();
    }

    protected function handleChatMessage(Stringable $text): void
    {
        // Log::info(json_encode($this->message, JSON_UNESCAPED_UNICODE));

        if ($this->message->contact()) {
            $phoneNumber = $this->message->contact()->phoneNumber();
            $message = $this->userService->register($phoneNumber, $this->message->from()->toArray());
            $this->reply($message);
        } else {
            $result = $this->userService->checkUser($this->message->from()->id());
            Log::info(json_encode($result, JSON_UNESCAPED_UNICODE));
            // Log::info($result);

            if (!$result['result']) {
                $this->reply($result['message']);
                return;
            } 

            if ($result['result']) {
                Log::info(json_encode($text, JSON_UNESCAPED_UNICODE));
                MessageService::saveMessage($text, $result['user_id']);
                // $this->reply($text);
                return;
            } else {
                $this->reply('Ваши сообщения не сохраняються в бд.');
                return;
            }
        }
    }

    public function verify(string $code)
    {   
        $this->reply($this->userService->verifyUser($this->message->from()->toArray(), $code));
    }
}
