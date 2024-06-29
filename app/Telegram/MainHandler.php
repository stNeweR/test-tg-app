<?php

namespace App\Telegram;

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
    public $service;

    public function __construct()
    {
        $this->service = new UserService();
    }

    public function start()
    {
        $this->chat->message('Здравствуйте, чтобы начать пользоваться ботом нужно авторизоваться. После того как вы отпарвите мне свой номер телефона, вам придет смс с кодом.')->replyKeyboard(ReplyKeyboard::make()->oneTime()->buttons([
           ReplyButton::make('Отправить свой номер телефона')->requestContact()
       ]))->send();
    }

    protected function handleChatMessage(Stringable $text): void
    {
        if ($this->message->contact()->phoneNumber()) {
            $phoneNumber = $this->message->contact()->phoneNumber();

            $message = $this->service->register($phoneNumber, $this->message->from()->toArray());

            $this->reply($message);
        } else {
            $this->reply('None...');
        }
    }

    public function verify(Stringable $text)
    {   
        
    }
}
