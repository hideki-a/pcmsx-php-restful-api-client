<?php

declare(strict_types=1);

namespace PowerCMSX\RESTfulAPI;

enum ApiMethod
{
    case DELETE;
    case GET;
    case INSERT;
    case LIST;
    case UPDATE;
    case SCHEME;
}
