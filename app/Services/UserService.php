<?php 

namespace App\Services;
use App\Models\User;
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
        
        $user = User::create([
            'first_name' => $userData['first_name'],
            'last_name' => $userData['last_name'],
            'user_name' => $userData['username'],
            'telegram_id' => $userData['id'],
            'phone_number' => $phoneNumber,
            'verify_code' => $verifyCode
        ]);
        Log::info(json_encode($user, JSON_UNESCAPED_UNICODE));

        return 'SMS отправлено успешно!';
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