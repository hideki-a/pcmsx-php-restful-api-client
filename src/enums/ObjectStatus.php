<?php

declare(strict_types=1);

namespace PowerCMSX;

enum ObjectStatus: int
{
    case Draft = 0;
    case Review = 1;
    case ApprovalPending = 2;
    case Reserved = 3;
    case Publish = 4;
    case Ended = 5;
}
