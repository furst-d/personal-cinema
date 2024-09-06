<?php

namespace App\Controller\V1\Admin\Video;

use App\Attribute\OpenApi\Request\Query\QueryFilter;
use App\Attribute\OpenApi\Request\Query\QueryFilterPropertyIds;
use App\Attribute\OpenApi\Request\Query\QueryLimit;
use App\Attribute\OpenApi\Request\Query\QueryOffset;
use App\Attribute\OpenApi\Request\Query\QueryOrderBy;
use App\Attribute\OpenApi\Request\Query\QuerySortBy;
use App\Attribute\OpenApi\Request\RequestBody;
use App\Attribute\OpenApi\Response\ResponseData;
use App\Attribute\OpenApi\Response\ResponseError;
use App\Attribute\OpenApi\Response\ResponseMessage;
use App\Controller\V1\Personal\BasePersonalController;
use App\DTO\Admin\Video\ConversionRequest;
use App\DTO\Admin\Video\VideoConversionQueryRequest;
use App\DTO\Filter\BatchDeleteFilterRequest;
use App\Entity\Video\Conversion;
use App\Exception\ApiException;
use App\Exception\InternalException;
use App\Exception\NotFoundException;
use App\Exception\UnauthorizedException;
use App\Helper\DTO\SortBy;
use App\Helper\Regex\RegexRoute;
use App\Service\Video\ConversionService;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;

#[Route('/admin/videos/conversions')]
class VideoConversionController extends BasePersonalController
{
    /**
     * @var ConversionService $conversionService
     */
    private ConversionService $conversionService;

    /**
     * @param ConversionService $conversionService
     */
    public function __construct(ConversionService $conversionService)
    {
        $this->conversionService = $conversionService;
    }

    #[OA\Get(
        description: "Retrieve a list of video conversions.",
        summary: "Get video conversions",
        tags: [VideoController::TAG],
    )]
    #[QueryLimit]
    #[QueryOffset]
    #[QuerySortBy(choices: [SortBy::ID, SortBy::WIDTH, SortBy::HEIGHT, SortBy::BANDWIDTH])]
    #[QueryOrderBy]
    #[ResponseData(entityClass: Conversion::class, groups: [Conversion::CONVERSION_READ], pagination: true, description: "List of video conversions")]
    #[ResponseError(exception: new UnauthorizedException())]
    #[ResponseError(exception: new InternalException())]
    #[Security(name: "Bearer")]
    #[Route('', name: 'admin_conversions', methods: ['GET'])]
    public function getConversions(VideoConversionQueryRequest $conversionQueryRequest): JsonResponse
    {
        $conversions = $this->conversionService->getConversions($conversionQueryRequest);
        return $this->re->withData($conversions, [Conversion::CONVERSION_READ]);
    }

    #[OA\Post(
        description: "Create a new video conversion.",
        summary: "Create video conversion",
        requestBody: new RequestBody(entityClass: ConversionRequest::class),
        tags: [VideoController::TAG],
    )]
    #[ResponseData(entityClass: Conversion::class, groups: [Conversion::CONVERSION_READ], collection: false, description: "Created video conversion")]
    #[ResponseError(exception: new UnauthorizedException())]
    #[ResponseError(exception: new InternalException())]
    #[Security(name: "Bearer")]
    #[Route('', name: 'admin_conversion_create', methods: ['POST'])]
    public function createConversion(ConversionRequest $conversionRequest): JsonResponse
    {
        $conversion = $this->conversionService->createConversion($conversionRequest);
        return $this->re->withData($conversion, [Conversion::CONVERSION_READ]);
    }

    #[OA\Delete(
        description: "Batch delete video conversions by their ids.",
        summary: "Delete video conversions",
        tags: [VideoController::TAG],
    )]
    #[QueryFilter(properties: [new QueryFilterPropertyIds()])]
    #[ResponseData(entityClass: Conversion::class, groups: [Conversion::CONVERSION_READ], description: "Deleted video conversions")]
    #[ResponseError(exception: new UnauthorizedException())]
    #[ResponseError(exception: new NotFoundException(ConversionService::SOME_NOT_FOUND_MESSAGE))]
    #[ResponseError(exception: new InternalException())]
    #[Security(name: "Bearer")]
    #[Route('', name: 'admin_conversions_batch_delete', methods: ['DELETE'])]
    public function batchDelete(BatchDeleteFilterRequest $filter): JsonResponse
    {
        try {
            $conversions = $this->conversionService->getConversionsByIds($filter->ids);

            foreach ($conversions as $conversion) {
                $this->conversionService->deleteConversion($conversion);
            }

            return $this->re->withData($conversions, [Conversion::CONVERSION_READ]);
        } catch (ApiException $e) {
            return $this->re->withException($e);
        }
    }

    #[OA\Get(
        description: "Retrieve a video conversion by id.",
        summary: "Get video conversion",
        tags: [VideoController::TAG],
    )]
    #[ResponseData(entityClass: Conversion::class, groups: [Conversion::CONVERSION_READ], collection: false, description: "Video conversion detail")]
    #[ResponseError(exception: new UnauthorizedException())]
    #[ResponseError(exception: new NotFoundException(ConversionService::NOT_FOUND_MESSAGE))]
    #[ResponseError(exception: new InternalException())]
    #[Security(name: "Bearer")]
    #[Route(RegexRoute::ID, name: 'admin_conversion', methods: ['GET'])]
    public function getConversionDetail(int $id): JsonResponse
    {
        try {
            $conversion = $this->conversionService->getConversionById($id);
            return $this->re->withData($conversion, [Conversion::CONVERSION_READ]);
        } catch (ApiException $e) {
            return $this->re->withException($e);
        }
    }

    #[OA\Put(
        description: "Updates video conversion by id.",
        summary: "Update video conversion",
        requestBody: new RequestBody(entityClass: ConversionRequest::class),
        tags: [VideoController::TAG],
    )]
    #[ResponseData(entityClass: Conversion::class, groups: [Conversion::CONVERSION_READ], collection: false, description: "Updated video conversion")]
    #[ResponseError(exception: new UnauthorizedException())]
    #[ResponseError(exception: new NotFoundException(ConversionService::NOT_FOUND_MESSAGE))]
    #[ResponseError(exception: new InternalException())]
    #[Security(name: "Bearer")]
    #[Route(RegexRoute::ID, name: 'admin_conversion_update', methods: ['PUT'])]
    public function updateConversion(int $id, ConversionRequest $conversionRequest): JsonResponse
    {
        try {
            $conversion = $this->conversionService->getConversionById($id);
            $this->conversionService->updateConversion($conversion,$conversionRequest);
            return $this->re->withData($conversion, [Conversion::CONVERSION_READ]);
        } catch (ApiException $e) {
            return $this->re->withException($e);
        }
    }

    #[OA\Delete(
        description: "Delete video conversion by ids.",
        summary: "Delete video conversion",
        tags: [VideoController::TAG],
    )]
    #[ResponseMessage(message: "Conversion deleted successfully")]
    #[ResponseError(exception: new UnauthorizedException())]
    #[ResponseError(exception: new NotFoundException(ConversionService::NOT_FOUND_MESSAGE))]
    #[ResponseError(exception: new InternalException())]
    #[Security(name: "Bearer")]
    #[Route(RegexRoute::ID, name: 'admin_conversion_delete', methods: ['DELETE'])]
    public function deleteConversion(int $id): JsonResponse
    {
        try {
            $conversion = $this->conversionService->getConversionById($id);
            $this->conversionService->deleteConversion($conversion);
            return $this->re->withMessage('Conversion deleted successfully');
        } catch (ApiException $e) {
            return $this->re->withException($e);
        }
    }
}
