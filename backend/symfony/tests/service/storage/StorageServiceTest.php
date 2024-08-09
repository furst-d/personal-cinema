<?php

namespace App\Tests\Service\Storage;

use App\Entity\Storage\Storage;
use App\Exception\FullStorageException;
use App\Exception\TooLargeException;
use App\Repository\Settings\SettingsRepository;
use App\Service\Storage\StorageService;
use PHPUnit\Framework\TestCase;

class StorageServiceTest extends TestCase
{
    private StorageService $storageService;
    private $mockSettingsRepository;

    protected function setUp(): void
    {
        $this->mockSettingsRepository = $this->createMock(SettingsRepository::class);
        $this->storageService = new StorageService($this->mockSettingsRepository);
    }

    public function testCheckStorageBeforeUploadSuccess()
    {
        $storage = $this->createMock(Storage::class);
        $storage->method('getMaxStorage')->willReturn(2000 * 1024 * 1024); // 2GB
        $storage->method('getUsedStorage')->willReturn(500 * 1024 * 1024); // 500MB

        $this->mockSettingsRepository->method('getMaxFileSize')->willReturn('500MB');

        $this->storageService->checkStorageBeforeUpload($storage, 300 * 1024 * 1024); // 300MB
        $this->assertTrue(true); // Test passes if no exception is thrown
    }

    public function testCheckStorageBeforeUploadThrowsFullStorageException()
    {
        $this->expectException(FullStorageException::class);

        $storage = $this->createMock(Storage::class);
        $storage->method('getMaxStorage')->willReturn(1000 * 1024 * 1024); // 1GB
        $storage->method('getUsedStorage')->willReturn(900 * 1024 * 1024); // 900MB

        $this->mockSettingsRepository->method('getMaxFileSize')->willReturn('500MB');

        $this->storageService->checkStorageBeforeUpload($storage, 200 * 1024 * 1024); // 200MB
    }

    public function testCheckStorageBeforeUploadThrowsTooLargeException()
    {
        $this->expectException(TooLargeException::class);

        $storage = $this->createMock(Storage::class);
        $storage->method('getMaxStorage')->willReturn(2000 * 1024 * 1024); // 2GB
        $storage->method('getUsedStorage')->willReturn(500 * 1024 * 1024); // 500MB

        $this->mockSettingsRepository->method('getMaxFileSize')->willReturn('300MB');

        $this->storageService->checkStorageBeforeUpload($storage, 400 * 1024 * 1024); // 400MB
    }

    public function testGetDefaultUserStorageLimit()
    {
        $this->mockSettingsRepository->method('getDefaultUserStorageLimit')->willReturn('2GB');
        $result = $this->storageService->getDefaultUserStorageLimit();
        $this->assertEquals(2 * 1024 * 1024 * 1024, $result);
    }
}
