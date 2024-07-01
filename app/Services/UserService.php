<?php 

namespace App\Services;
use App\Models\User;
use DefStudio\Telegraph\Facades\Telegraph;
use GuzzleHttp\Client;
use Log;


class UserService
{
    public function register($phoneNumber, array $userData)
    {       
        $verifyCode = rand(100000, 999999);
        $response = $this->sendCode($phoneNumber, $verifyCode);
        Log::info(json_encode($phoneNumber, JSON_UNESCAPED_UNICODE));
        Log::info(json_encode($response, JSON_UNESCAPED_UNICODE));
        
        if ($response->getStatusCode() != 200) {
            return 'Ошибка при отправке SMS.';
        } 
        
        $checkUser = User::query()->where('telegram_id', '=', $userData['id'])->get();

        if ($checkUser->isNotEmpty()) {
            $checkUser->first()->update([
                'verify_code' => $verifyCode,
                'phone_verified' => false
            ]);

            return 'Вы уже были авторизованы в системе. Мы выслали вам новый код подтверждения. Пока вы не выиполните команду /verify с этим кодом выши сообщения не будут записаны ';
        }

        $user = User::create([
            'first_name' => $userData['first_name'],
            'last_name' => $userData['last_name'],
            'user_name' => $userData['username'],
            'telegram_id' => $userData['id'],
            'phone_number' => $phoneNumber,
            'verify_code' => $verifyCode
        ]);
        Log::info(json_encode($user, JSON_UNESCAPED_UNICODE));

        return 'SMS с кодом отправлено успешно! Выполните команду /verify и напишите после команды код который вы получили в смс';
    }

    public function verifyUser(array $user, $code)
    {
        Log::info(json_encode($user, JSON_UNESCAPED_UNICODE));

        $findUser = User::query()->where('telegram_id', '=', $user['id'])->first();

        if ($findUser->phone_verified) {
            return 'Пользователь уже авторизован. Теперь все ваши сообщения будут сохраняться в бд.';
        }

        if (!$findUser) {
            return 'Пользователя нет в базе. Сначала выполните команду /start и авторизуйтесь';
        } 

        if ($code === '/verify') {
            return 'Вы не ввели код из смс. А если смс не пришла, то снова воспользуйтесь командой /start.';
        }

        if ($findUser->verify_code == $code) {
            $findUser->update([
                'phone_verified' => true
            ]);
            Log::info(json_encode($findUser->verify_code, JSON_UNESCAPED_UNICODE));
            return 'Вы успешно авторизованы! Теперь все сообщения будут сохраняться в базу данных';
        }    

        return $code . ' - это не верный код. Проверьте пожалуйста смс или снова выолните команду /start.';
    }

    public function checkUser($senderId)
    {
        $sender = User::query()->where('telegram_id', '=', $senderId)->get();

        Log::info(json_encode($sender->first(), JSON_UNESCAPED_UNICODE));

        if ($sender->isEmpty()) {
            return [
                'result' => false,
                'message' => 'Пользователя нет в базе данных.'
            ];
        }

        // Log::info(json_encode($sender->toArray(), JSON_UNESCAPED_UNICODE));

        $sender = $sender->first();

        if ($sender->phone_verified === false) {
            return [
                'result' => false,
                'message' => 'Пользователь есть в базе данных, но не авторизован. Сообщения ну будут записаны в бд. Выполните команду /verify и введите код который вы получили в смс.'
            ];
        }

        Log::info('11111');

        return [
            'result' => true,
            'user_id' => $sender->id
        ];
    }

    public function sendCode($phoneNumber, $code)
    {
        $mail = env('SMS_AERO_MAIL');
        $key = env('SMS_AERO_KEY');
        $client = new Client();

        return $client->request('GET', "https://$mail:$key@gate.smsaero.ru/v2/sms/send?number=79135609590&text=Привет!&sign=", [    
            'query' => [
                'number' => $phoneNumber,
                'text' => $code,
                'sign' => 'SMS Aero'
            ]
        ]);
    }
}