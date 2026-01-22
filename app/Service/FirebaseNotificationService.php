<?php

namespace App\Service;

use Google\Auth\Credentials\ServiceAccountCredentials;
use Illuminate\Support\Facades\Http;

class FirebaseNotificationService
{
    private function accessToken()
    {
        $credentials = new ServiceAccountCredentials(
            ['https://www.googleapis.com/auth/firebase.messaging'],
            json_decode(
                file_get_contents(base_path(env('FIREBASE_CREDENTIALS'))),
                true
            )
        );

        $credentials->fetchAuthToken();
        return $credentials->getLastReceivedToken()['access_token'];
    }

    public function sendToToken(string $token, string $title, string $body)
    {
        return Http::withToken($this->accessToken())
            ->post(
                "https://fcm.googleapis.com/v1/projects/" . env('FIREBASE_PROJECT_ID') . "/messages:send",
                [
                    "message" => [
                        "token" => $token,
                        "notification" => [
                            "title" => $title,
                            "body"  => $body,
                        ]
                    ]
                ]
            )
            ->json();
    }
}
