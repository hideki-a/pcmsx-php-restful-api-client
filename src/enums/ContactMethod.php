<?php

declare(strict_types=1);

namespace PowerCMSX\RESTfulAPI;

enum ContactMethod
{
    case TOKEN;
    case CONFIRM;
    case SUBMIT;
}
