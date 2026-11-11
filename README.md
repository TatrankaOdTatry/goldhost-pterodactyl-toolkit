# GoldHost Pterodactyl Toolkit

A small PHP client for reading server information through the Pterodactyl Application API.

This is an independent community project and is not affiliated with or endorsed by the Pterodactyl project.

## Requirements

- PHP 8.1+
- Composer
- A Pterodactyl Application API key

## Installation

Clone or download this repository, then install its dependencies:

```bash
git clone https://github.com/TatrankaOdTatry/goldhost-pterodactyl-toolkit.git
cd goldhost-pterodactyl-toolkit
composer install
```

The package is not currently published on Packagist, so `composer require` is not available yet.

## Usage

```php
<?php

require __DIR__ . '/vendor/autoload.php';

use GoldHost\PterodactylToolkit\PterodactylClient;

$panelUrl = getenv('PTERODACTYL_URL');
$apiKey = getenv('PTERODACTYL_APPLICATION_API_KEY');

if (!$panelUrl || !$apiKey) {
    throw new RuntimeException('Set PTERODACTYL_URL and PTERODACTYL_APPLICATION_API_KEY.');
}

$client = new PterodactylClient($panelUrl, $apiKey);

// List servers
$response = $client->listServers();

foreach ($response['data'] ?? [] as $item) {
    $server = $item['attributes'] ?? [];
    echo ($server['name'] ?? 'Unnamed server') . PHP_EOL;
}

// Retrieve one server by ID
$server = $client->getServer(123);
print_r($server['attributes'] ?? []);
```

## Scope

The first version intentionally provides only two read-only operations:

- `listServers(?int $page = null)`
- `getServer(int|string $serverId)`

It does not create, modify, suspend, delete or reinstall servers.

## Running tests

```bash
composer install
composer test
```

## Security

- Use an Application API key with the minimum permissions needed.
- Keep API keys in environment variables or a secrets manager.
- Never commit `.env` files, API keys or panel credentials.
- Use HTTPS when connecting to a remote panel.

## License

MIT. See [LICENSE](LICENSE).