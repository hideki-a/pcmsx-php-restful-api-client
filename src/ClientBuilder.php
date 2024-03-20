<?php

declare(strict_types=1);

namespace PowerCMSX\RESTfulAPI;

require_once 'Client.php';

class ClientBuilder
{
    public static function create(): Client
    {
        return new Client();
    }
}
