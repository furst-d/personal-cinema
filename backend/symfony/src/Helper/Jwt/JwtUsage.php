<?php

namespace App\Helper\Jwt;

enum JwtUsage: int
{
    case USAGE_API_ACCESS = 1;
    case USAGE_API_REFRESH = 2;
    case USAGE_ACCOUNT_ACTIVATION = 3;
    case USAGE_PASSWORD_RESET = 4;
    case USAGE_UPLOAD = 5;
    case USAGE_VIDEO_ACCESS = 6;
    case USAGE_SHARE_VIDEO = 7;
    case USAGE_SHARE_FOLDER = 8;
}
