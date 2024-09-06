<?php

namespace App\Controller\V1\Admin\Setting;

use App\Attribute\OpenApi\Request\Query\QueryFilter;
use App\Attribute\OpenApi\Request\Query\QueryFilterProperty;
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
use App\DTO\Admin\Setting\SettingQueryRequest;
use App\DTO\Admin\Setting\SettingRequest;
use App\DTO\Filter\BatchDeleteFilterRequest;
use App\Entity\Settings\Settings;
use App\Exception\ApiException;
use App\Exception\InternalException;
use App\Exception\NotFoundException;
use App\Exception\UnauthorizedException;
use App\Helper\DTO\SortBy;
use App\Helper\Regex\RegexRoute;
use App\Service\Setting\SettingService;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;

#[Route('/admin/settings')]
class SettingController extends BasePersonalController
{
    /**
     * @var SettingService $settingService
     */
    private SettingService $settingService;

    public const TAG = 'admin/settings';

    /**
     * @param SettingService $settingService
     */
    public function __construct(SettingService $settingService)
    {
        $this->settingService = $settingService;
    }

    #[OA\Get(
        description: "Retrieve a list of settings.",
        summary: "Get settings",
        tags: [self::TAG],
    )]
    #[QueryLimit]
    #[QueryOffset]
    #[QuerySortBy(choices: [SortBy::ID, SortBy::KEY, SortBy::VALUE])]
    #[QueryOrderBy]
    #[ResponseData(entityClass: Settings::class, pagination: true, description: "List of settings")]
    #[ResponseError(exception: new UnauthorizedException())]
    #[ResponseError(exception: new InternalException())]
    #[Security(name: "Bearer")]
    #[Route('', name: 'admin_settings', methods: ['GET'])]
    public function getSettings(SettingQueryRequest $queryRequest): JsonResponse
    {
        $settings = $this->settingService->getSettings($queryRequest);
        return $this->re->withData($settings);
    }

    #[OA\Post(
        description: "Create a new setting.",
        summary: "Create setting",
        requestBody: new RequestBody(entityClass: SettingRequest::class),
        tags: [self::TAG],
    )]
    #[ResponseData(entityClass: Settings::class, collection: false, description: "Created setting")]
    #[ResponseError(exception: new UnauthorizedException())]
    #[ResponseError(exception: new InternalException())]
    #[Security(name: "Bearer")]
    #[Route('', name: 'admin_setting_create', methods: ['POST'])]
    public function createSetting(SettingRequest $settingRequest): JsonResponse
    {
        $setting = $this->settingService->createSetting($settingRequest->key, $settingRequest->value);
        return $this->re->withData($setting);
    }

    #[OA\Delete(
        description: "Batch delete settings by their ids.",
        summary: "Delete settings",
        tags: [self::TAG],
    )]
    #[QueryFilter(properties: [new QueryFilterPropertyIds()])]
    #[ResponseData(entityClass: Settings::class, description: "Deleted settings")]
    #[ResponseError(exception: new UnauthorizedException())]
    #[ResponseError(exception: new NotFoundException(SettingService::SOME_NOT_FOUND_MESSAGE))]
    #[ResponseError(exception: new InternalException())]
    #[Security(name: "Bearer")]
    #[Route('', name: 'admin_settings_batch_delete', methods: ['DELETE'])]
    public function batchDelete(BatchDeleteFilterRequest $filter): JsonResponse
    {
        try {
            $settings = $this->settingService->getSettingsByIds($filter->ids);

            foreach ($settings as $setting) {
                $this->settingService->deleteSetting($setting);
            }

            return $this->re->withData($settings);
        } catch (ApiException $e) {
            return $this->re->withException($e);
        }
    }

    #[OA\Get(
        description: "Retrieve a setting by id.",
        summary: "Get setting",
        tags: [self::TAG],
    )]
    #[ResponseData(entityClass: Settings::class, collection: false, description: "Setting detail")]
    #[ResponseError(exception: new UnauthorizedException())]
    #[ResponseError(exception: new NotFoundException(SettingService::NOT_FOUND_MESSAGE))]
    #[ResponseError(exception: new InternalException())]
    #[Security(name: "Bearer")]
    #[Route(RegexRoute::ID, name: 'admin_setting', methods: ['GET'])]
    public function getSettingDetail(int $id): JsonResponse
    {
        try {
            return $this->re->withData($this->settingService->getSettingById($id));
        } catch (ApiException $e) {
            return $this->re->withException($e);
        }
    }

    #[OA\Put(
        description: "Updates setting by id.",
        summary: "Update setting",
        requestBody: new RequestBody(entityClass: SettingRequest::class),
        tags: [self::TAG],
    )]
    #[ResponseData(entityClass: Settings::class, collection: false, description: "Updated setting")]
    #[ResponseError(exception: new UnauthorizedException())]
    #[ResponseError(exception: new NotFoundException(SettingService::NOT_FOUND_MESSAGE))]
    #[ResponseError(exception: new InternalException())]
    #[Security(name: "Bearer")]
    #[Route(RegexRoute::ID, name: 'admin_setting_update', methods: ['PUT'])]
    public function updateSetting(int $id, SettingRequest $settingRequest): JsonResponse
    {
        try {
            $setting = $this->settingService->getSettingById($id);
            $this->settingService->updateSetting($setting, $settingRequest);
            return $this->re->withData($setting);
        } catch (ApiException $e) {
            return $this->re->withException($e);
        }
    }

    #[OA\Delete(
        description: "Delete setting by ids.",
        summary: "Delete setting",
        tags: [self::TAG],
    )]
    #[ResponseMessage(message: "Setting deleted successfully")]
    #[ResponseError(exception: new UnauthorizedException())]
    #[ResponseError(exception: new NotFoundException(SettingService::NOT_FOUND_MESSAGE))]
    #[ResponseError(exception: new InternalException())]
    #[Security(name: "Bearer")]
    #[Route(RegexRoute::ID, name: 'admin_setting_delete', methods: ['DELETE'])]
    public function deleteSetting(int $id): JsonResponse
    {
        try {
            $setting = $this->settingService->getSettingById($id);
            $this->settingService->deleteSetting($setting);
            return $this->re->withMessage('Setting deleted successfully');
        } catch (ApiException $e) {
            return $this->re->withException($e);
        }
    }
}
