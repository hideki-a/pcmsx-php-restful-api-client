<?php

declare(strict_types=1);

namespace PowerCMSX\RESTfulAPI;

enum ApiMethod
{
    case Delete;
    case Get;
    case Insert;
    case List;
    case Update;
    case Scheme;
}
