<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class notificationController extends Controller
{
    function sendFCM()
    {
        $url = 'https://fcm.googleapis.com/fcm/send';

        $serverKey = 'AAAAY2qDDgY:APA91bEdlyP6LLhBp8rNaaMAS-i1neMVlHQc0oNsgKq5HmNjteOWeHv_fzqWcXAj7TvO4CPIp2CqZQqXo1AMSLJLAa7wXUDCBbQJPk5A0oYUr2JQ7SLwBTRaBOUB3tlRQh2h-Fb3eF65';

        $headers = [
            'Authorization:key=' . $serverKey,
            'Content-Type:application/json'
        ];

        $notifData = [
            'title' => 'My new notification',
            'body' => 'My new notification body',
        ];

        $dataPayload = [
            'to' => 'VIP',
            'date' => '2023-01-01',
        ];

        $notifBody = [
            'notification' => $notifData,
            'data' => $dataPayload, //optional
            'time_to_live' => 3600, //optional
            'to' => 'ce9FAt9DRc2yDpGXJpZKJ9:APA91bHOTUrIrZsun2hwz_XJubUC4woBfyUC_-CTQFhPCn6TmOMtVPwO5ocCkjJSSqFriBdU8B0eDb-pPLU1TZUvCU42yDs7mzFcc0RlzkyMH011TkY8BIxS4ct5JmF9tuM6G5zzFiPb'
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $notifBody);

        //execute
        $result = curl_exec($ch);
        print($result);

        curl_close($ch);
    }
}
