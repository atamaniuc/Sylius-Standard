<?php

declare(strict_types=1);

namespace App\Tests\Behat\Trait;

use Symfony\Component\Panther\Client;
use Symfony\Component\Panther\PantherTestCaseTrait;

trait PantherTrait
{
    use PantherTestCaseTrait;

    protected static function createPantherClient(): Client
    {
        return Client::createFirefoxClient();
    }
}
