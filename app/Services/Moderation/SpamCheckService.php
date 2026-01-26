<?php

namespace App\Services\Moderation;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class SpamCheckService
{
    public function check(string $text): array
    {
        $response = Http::timeout(3)->post(
            'http://127.0.0.1:8001/check',
            ['text' => $text]
        );

        if (! $response->successful()) {
            throw new RuntimeException('Moderation service unavailable');
        }

        return [
            'flagged' => $response->json('flagged'),
            'meta'    => $response->json('predictions'),
        ];
    }

    // public function check(string $text): array
    // {
    //     $response = Http::withToken(config('services.openai.key'))
    //         ->post('https://api.openai.com/v1/moderations', [
    //             'model' => 'omni-moderation-latest',
    //             'input' => $text,
    //         ]);

    //     $result = $response['results'][0];

    //     return [
    //         'flagged' => $result['flagged'],
    //         'scores' => $result['category_scores'],
    //     ];
    // }

}
