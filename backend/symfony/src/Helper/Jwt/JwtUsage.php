<?php

namespace App\Helper\Jwt;

enum JwtUsage: int
{
    case USAGE_API_ACCESS = 1;
    case USAGE_API_REFRESH = 2;
}
