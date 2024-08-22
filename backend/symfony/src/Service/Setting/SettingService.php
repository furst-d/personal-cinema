<?php

namespace App\Service\Setting;

use App\DTO\Admin\Setting\SettingRequest;
use App\DTO\PaginatorRequest;
use App\Entity\Settings\Settings;
use App\Exception\NotFoundException;
use App\Helper\DTO\PaginatorResult;
use App\Repository\Settings\SettingsRepository;

class SettingService
{
    /**
     * @var SettingsRepository
     */
    private SettingsRepository $settingsRepository;

    /**
     * @param SettingsRepository $settingsRepository
     */
    public function __construct(SettingsRepository $settingsRepository)
    {
        $this->settingsRepository = $settingsRepository;
    }

    /**
     * @param PaginatorRequest $paginatorRequest
     * @return PaginatorResult<Settings>
     */
    public function getSettings(PaginatorRequest $paginatorRequest): PaginatorResult
    {
        return $this->settingsRepository->getSettings($paginatorRequest);
    }

    /**
     * @param string $key
     * @param string $value
     * @return Settings
     */
    public function createSetting(string $key, string $value): Settings
    {
        $setting = new Settings($key, $value);
        $this->settingsRepository->save($setting);
        return $setting;
    }

    /**
     * @param int[] $ids
     * @return Settings[]
     * @throws NotFoundException
     */
    public function getSettingsByIds(array $ids): array
    {
        $settings = $this->settingsRepository->findByIds($ids);

        if (count($settings) !== count($ids)) {
            throw new NotFoundException("Some settings not found.");
        }

        return $settings;
    }

    /**
     * @param Settings $setting
     * @return void
     */
    public function deleteSetting(Settings $setting): void
    {
        $this->settingsRepository->delete($setting);
    }

    /**
     * @param int $id
     * @return Settings
     * @throws NotFoundException
     */
    public function getSettingById(int $id): Settings
    {
        $setting = $this->settingsRepository->find($id);

        if (!$setting) {
            throw new NotFoundException("Setting not found.");
        }

        return $setting;
    }

    /**
     * @param Settings $setting
     * @param SettingRequest $settingRequest
     * @return void
     */
    public function updateSetting(Settings $setting, SettingRequest $settingRequest): void
    {
        $setting->setKey($settingRequest->key);
        $setting->setValue($settingRequest->value);
        $this->settingsRepository->save($setting);
    }
}
