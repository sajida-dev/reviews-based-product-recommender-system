<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Minimal Qdrant REST client (cosine search + upsert). Point id = product id.
 */
class QdrantHttpClient
{
    public function __construct(
        protected string $baseUrl,
        protected ?string $apiKey = null,
    ) {}

    public static function fromConfig(): self
    {
        $host = rtrim((string) config('recommendation.qdrant_host'), '/');
        $port = config('recommendation.qdrant_port');
        $key = config('recommendation.qdrant_api_key');

        return new self(
            "{$host}:{$port}",
            is_string($key) && $key !== '' ? $key : null,
        );
    }

    /**
     * @param  list<array{id:int|string, vector:list<float>, payload?:array}>  $points
     */
    public function upsertPoints(string $collection, array $points): void
    {
        $client = $this->httpClient();
        $url = "{$this->baseUrl}/collections/{$collection}/points?wait=true";
        $body = [
            'points' => array_map(function (array $p) {
                return [
                    'id' => $p['id'],
                    'vector' => $p['vector'],
                    'payload' => $p['payload'] ?? new \stdClass,
                ];
            }, $points),
        ];

        $response = $client->put($url, $body);

        if (! $response->successful()) {
            throw new RuntimeException('Qdrant upsert failed: '.$response->body());
        }
    }

    /**
     * @param  list<float>  $vector
     * @return array{result: list<array{id: mixed, score?: float}>}
     */
    public function search(string $collection, array $vector, int $limit): array
    {
        $client = $this->httpClient();
        $url = "{$this->baseUrl}/collections/{$collection}/points/search";
        $response = $client->post($url, [
            'vector' => $vector,
            'limit' => $limit,
            'with_payload' => true,
        ]);

        if (! $response->successful()) {
            throw new RuntimeException('Qdrant search failed: '.$response->body());
        }

        /** @var array{result?: list<array{id: mixed, score?: float}>} $json */
        $json = $response->json();

        return ['result' => $json['result'] ?? []];
    }

    private function httpClient(): \Illuminate\Http\Client\PendingRequest
    {
        $req = Http::timeout(30)->acceptJson()->asJson();
        if ($this->apiKey) {
            $req = $req->withHeader('api-key', $this->apiKey);
        }

        return $req;
    }
}
