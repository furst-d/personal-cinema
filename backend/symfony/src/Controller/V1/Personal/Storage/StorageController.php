<?php

namespace App\Controller\V1\Personal\Storage;

use App\Attribute\OpenApi\Response\ResponseData;
use App\Attribute\OpenApi\Response\ResponseError;
use App\Controller\V1\Personal\BasePersonalController;
use App\DTO\Storage\StorageInfoResponse;
use App\Exception\InternalException;
use App\Exception\UnauthorizedException;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/personal/storage')]
class StorageController extends BasePersonalController
{
    public const TAG = 'personal/storage';

    #[OA\Get(
        description: "Retrieve a information about user's storage.",
        summary: "Get user's storage info",
        tags: [self::TAG],
    )]
    #[ResponseData(entityClass: StorageInfoResponse::class, collection: false, description: "User's storage info")]
    #[ResponseError(exception: new UnauthorizedException())]
    #[ResponseError(exception: new InternalException())]
    #[Security(name: 'Bearer')]
    #[Route('', name: 'user_storage', methods: ['GET'])]
    public function getStorageInfo(Request $request): JsonResponse
    {
        $account = $this->getAccount($request);
        $storage = $account->getStorage();

        return $this->re->withData(new StorageInfoResponse($storage->getMaxStorage(), $storage->getUsedStorage()));
    }
}
