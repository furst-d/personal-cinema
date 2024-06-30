<?php

namespace App\Helper\Jwt;

enum JwtExpiration: int
{
    case EXPIRATION_30_SECONDS = 30;
    case EXPIRATION_1_MINUTE = 60;
    case EXPIRATION_10_MINUTES = 600;
    case EXPIRATION_30_MINUTES = 1800;
    case EXPIRATION_1_HOUR = 3600;
    case EXPIRATION_1_DAY = 86400;
    case EXPIRATION_1_YEAR = 31536000;
}
