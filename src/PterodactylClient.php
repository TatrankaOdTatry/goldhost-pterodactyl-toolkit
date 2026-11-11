<?php

declare(strict_types=1);

namespace GoldHost\PterodactylToolkit;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\ClientInterface;
use JsonException;
use UnexpectedValueException;

final class PterodactylClient
{
    private ClientInterface $http;

    public function __construct(
        string $panelUrl,
        string $applicationApiKey,
        ?ClientInterface $httpClient = null
    ) {
        if (trim($panelUrl) === '' || trim($applicationApiKey) === '') {
            throw new \InvalidArgumentException('Panel URL and API key are required.');
        }

        $this->http = $httpClient ?? new HttpClient([
            'base_uri' => rtrim($panelUrl, '/') . '/',
            'headers' => [
                'Authorization' => 'Bearer ' . $applicationApiKey,
                'Accept' => 'Application/vnd.pterodactyl.v1+json',
            ],
            'timeout' => 15,
        ]);
    }

    /**
     * Retrieve a page of servers from the Pterodactyl Application API.
     *
     * The raw API response is returned, including data and pagination metadata.
     *
     * @return array<string, mixed>
     */
    public function listServers(?int $page = null): array
    {
        $query = $page !== null ? ['page' => $page] : [];

        return $this->get('api/application/servers', $query);
    }

    /**
     * Retrieve one server by its Pterodactyl server ID.
     *
     * @return array<string, mixed>
     */
    public function getServer(int|string $serverId): array
    {
        $serverId = trim((string) $serverId);

        if ($serverId === '') {
            throw new \InvalidArgumentException('Server ID cannot be empty.');
        }

        return $this->get('api/application/servers/' . rawurlencode($serverId));
    }

    /**
     * @param array<string, int> $query
     * @return array<string, mixed>
     */
    private function get(string $path, array $query = []): array
    {
        $response = $this->http->request('GET', $path, ['query' => $query]);

        try {
            $payload = json_decode(
                (string) $response->getBody(),
                true,
                512,
                JSON_THROW_ON_ERROR
            );
        } catch (JsonException $exception) {
            throw new UnexpectedValueException(
                'The Pterodactyl API returned invalid JSON.',
                previous: $exception
            );
        }

        if (!is_array($payload)) {
            throw new UnexpectedValueException('The Pterodactyl API response must be a JSON object.');
        }

        return $payload;
    }
}