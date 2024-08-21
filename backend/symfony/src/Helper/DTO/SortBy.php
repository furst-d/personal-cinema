<?php

namespace App\Helper\DTO;

enum SortBy: string
{
    case ID = 'id';
    case NAME = 'name';
    case EMAIL = 'email';
    case CREATE_DATE = 'createdAt';
    case UPDATE_DATE = 'updatedAt';
    case IS_ACTIVE = 'isActive';
    case LENGTH = 'length';
    case SIZE = 'size';
}
