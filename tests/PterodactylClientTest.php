<?php

declare(strict_types=1);

namespace GoldHost\PterodactylToolkit\Tests;

use GoldHost\PterodactylToolkit\PterodactylClient;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

final class PterodactylClientTest extends TestCase
{
    public function testListServersReturnsApiPayload(): void
    {
        $payload = [
            'data' => [
                ['attributes' => ['id' => 12, 'name' => 'Test server']],
            ],
        ];

        $mock = new MockHandler([
            new Response(200, [], json_encode($payload, JSON_THROW_ON_ERROR)),
        ]);

        $client = new PterodactylClient(
            'https://panel.example',
            'test-key',
            new HttpClient(['base_uri' => 'https://panel.example/', 'handler' => HandlerStack::create($mock)])
        );

        self::assertSame($payload, $client->listServers());
    }

    public function testGetServerReturnsApiPayload(): void
    {
        $payload = [
            'attributes' => ['id' => 42, 'name' => 'Example server'],
        ];

        $mock = new MockHandler([
            new Response(200, [], json_encode($payload, JSON_THROW_ON_ERROR)),
        ]);

        $client = new PterodactylClient(
            'https://panel.example',
            'test-key',
            new HttpClient(['base_uri' => 'https://panel.example/', 'handler' => HandlerStack::create($mock)])
        );

        self::assertSame($payload, $client->getServer(42));
    }
}