<?php declare(strict_types = 1);

namespace Area51\Api;

use Area51\Config;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class RobotApiClient
{
    public function __construct(
        private HttpClientInterface $client
    ) {}

    public function create() : string
    {
        $response = $this->client->request('POST', Config::API_URL, [
            'json' => ['email' => Config::EMAIL]
        ]);

        $robotId = null;
        if ($response->getStatusCode() === 200)
        {
            $data = $response->toArray();

            $robotId =  $data["id"] ?? null;
        }

        if ($robotId === null) 
        {
            return static::create();
        }

        return $robotId;
    }

    public function move(string $robotId, Direction $direction, int $distance) : int|null
    {
        $response = $this->client->request('PUT', Config::API_URL . $robotId . '/move', [
            'json' => [
                'direction' => $direction->value,
                'distance' => $distance
            ]
        ]);

        if ($response->getStatusCode() === 410)
        {
            throw new RobotOfflineException();
        }

        if ($response->getStatusCode() === 200)
        {
            $data = $response->toArray();

            return $data["distance"] ?? null;
        }

        return null;
    }

    public function escape(string $robotId) : bool|null
    {
        $response = $this->client->request('PUT', Config::API_URL . $robotId . '/escape', [
            'json' => [
                'salary' => 500 * 160
            ]
        ]);

        if ($response->getStatusCode() === 200)
        {
            $data = $response->toArray();

            $success = $data["success"] ?? null;

            return $success;
        }

        return false;
    }
}