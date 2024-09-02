<?php

namespace App\Attribute\OpenApi\Response;

use Attribute;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;

#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class ResponseData extends ResponseBase
{

    /**
     * @param string $entityClass
     * @param array $groups
     * @param bool $pagination
     * @param bool $collection
     * @param string $description
     * @param int $responseCode
     */
    public function __construct(
        string $entityClass,
        array $groups = [],
        bool $pagination = false,
        bool $collection = true,
        string $description = 'Ok',
        int $responseCode = Response::HTTP_OK
    ) {
        $payloadProperties = [
            new OA\Property(property: 'count', type: 'integer', example: 1),
        ];

        if ($pagination) {
            $payloadProperties[] = new OA\Property(property: 'totalCount', type: 'integer', nullable: true, example: 100);
        }

        $dataProperty = $collection
            ? new OA\Property(
                property: 'data',
                type: 'array',
                items: new OA\Items(
                    ref: new Model(
                        type: $entityClass,
                        groups: empty($groups) ? null : $groups
                    )
                )
            )
            : new OA\Property(
                property: 'data',
                ref: new Model(
                    type: $entityClass,
                    groups: empty($groups) ? null : $groups
                )
            );

        $payloadProperties[] = $dataProperty;

        parent::__construct(
            responseCode: $responseCode,
            payloadProperties: $payloadProperties,
            requiredPayloadProperties: ['count', 'data'],
            description: $description
        );
    }
}
