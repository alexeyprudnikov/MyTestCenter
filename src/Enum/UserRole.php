<?php

namespace App\Enum;

enum UserRole: string
{
    case USER = "ROLE_USER";
    case ADMIN = "ROLE_ADMIN";
    case SUPER_ADMIN = "ROLE_SUPER_ADMIN";
}
