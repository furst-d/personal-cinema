<?php

namespace App\Controller\V1\Admin\Setting;

use App\Controller\V1\Personal\BasePersonalController;
use App\DTO\Admin\Setting\SettingQueryRequest;
use App\DTO\Admin\Setting\SettingRequest;
use App\DTO\Filter\BatchDeleteFilterRequest;
use App\Exception\ApiException;
use App\Helper\Regex\RegexRoute;
use App\Service\Locator\BaseControllerLocator;
use App\Service\Setting\SettingService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/settings')]
class SettingController extends BasePersonalController
{
    /**
     * @var SettingService $settingService
     */
    private SettingService $settingService;

    /**
     * @param BaseControllerLocator $locator
     * @param SettingService $settingService
     */
    public function __construct(
        BaseControllerLocator $locator,
        SettingService $settingService,
    )
    {
        parent::__construct($locator);
        $this->settingService = $settingService;
    }

    #[Route('', name: 'admin_settings', methods: ['GET'])]
    public function getSettings(SettingQueryRequest $queryRequest): JsonResponse
    {
        $settings = $this->settingService->getSettings($queryRequest);
        return $this->re->withData($settings);
    }

    #[Route('', name: 'admin_setting_create', methods: ['POST'])]
    public function createSetting(SettingRequest $settingRequest): JsonResponse
    {
        $setting = $this->settingService->createSetting($settingRequest->key, $settingRequest->value);
        return $this->re->withData($setting);
    }

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

    #[Route(RegexRoute::ID, name: 'admin_setting', methods: ['GET'])]
    public function getSettingDetail(int $id): JsonResponse
    {
        try {
            return $this->re->withData($this->settingService->getSettingById($id));
        } catch (ApiException $e) {
            return $this->re->withException($e);
        }
    }

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
