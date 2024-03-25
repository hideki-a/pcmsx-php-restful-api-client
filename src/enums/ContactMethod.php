<?php

declare(strict_types=1);

namespace PowerCMSX\RESTfulAPI;

enum ContactMethod
{
    case Token;
    case Confirm;
    case Submit;
}
