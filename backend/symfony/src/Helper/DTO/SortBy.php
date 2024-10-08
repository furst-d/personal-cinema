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
    case WIDTH = 'width';
    case HEIGHT = 'height';
    case BANDWIDTH = 'bandwidth';
    case KEY = 'key';
    case VALUE = 'value';
    case MAX_STORAGE = 'maxStorage';
    case USED_STORAGE = 'usedStorage';
    case FILL_SIZE = 'fillSize';
    case PRICE_CZK = 'priceCzk';

    case PERCENTAGE_DISCOUNT = 'percentageDiscount';
    case DISCOUNT_EXPIRATION_AT = 'discountExpirationAt';
}
