<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>


# Решение тестового задания

Вот [ссылка](https://docs.google.com/document/d/1LiXGvyPz69vIsmcOg60_IPfJXLRZJweTE6y7ER4lAEA/edit) на тестовое задание.

# Как запустить 

1) Склонируйте себе репозиторий

2) Сделайте себе файл .env

3) Выполните команду 
```
sail up -d
```
4) Команду
```
composer install
```

5) Вам нужно как-то получить ssl сертификат. Можно использовать [ngrok](https://ngrok.com/) или [tuna](https://tuna.am/)

6) Выполните команды для телеграм [бота](https://docs.defstudio.it/telegraph/v1/index)

7) Сделайте аккаунт на [SMS Aero](https://smsaero.ru/) (там дается 10 бесплатных смс для тестирования)

8) Задайте значние переменным в .env файле 
```
SMS_AERO_KEY=ключ от sms aero
SMS_AERO_MAIL=логин от sms aero
```

10) Выполните команды для [Moonshine](https://moonshine-laravel.com/docs/resource/getting-started/installation)

11) Выполните все миграции командой 
```
sail artisan migrate
```

Все, приложение готово к работе:)

# Небольшая инструкция
Для того чтобы телеграм бот начал работать выполните команду /start. После этого вам будет предложено поделиться своим номером с ботом. 

Если вы подключили SMS Aero то тогда к вам должна придти смс с кодом. Выполните команду /verify {код из смс}. Чтобы авторизоваться в боте. После всех этих манипуляций бота начнет сохранять все ваши сообщения в базу данных. 

Если вы будете не авторизованы, то тогда бот будет вам говорить авторизоваться и не будет сохранять ваши сообщения. 
