<?php

namespace App\Tests\Service\Storage;

use App\Entity\Account\Account;
use App\Entity\Storage\Storage;
use App\Entity\Storage\StorageCardPayment;
use App\Entity\Storage\StorageUpgrade;
use App\Entity\Storage\StorageUpgradePrice;
use App\Exception\ConflictException;
use App\Exception\FullStorageException;
use App\Exception\NotFoundException;
use App\Exception\TooLargeException;
use App\Helper\Storage\StoragePaymentMetadata;
use App\Helper\Storage\StoragePaymentType;
use App\Repository\Settings\SettingsRepository;
use App\Repository\Storage\StorageCardPaymentRepository;
use App\Repository\Storage\StorageRepository;
use App\Repository\Storage\StorageUpgradePriceRepository;
use App\Repository\Storage\StorageUpgradeRepository;
use App\Service\Storage\StorageService;
use PHPUnit\Framework\TestCase;

class StorageServiceTest extends TestCase
{
    private StorageService $storageService;
    private $mockSettingsRepository;
    private $mockStorageRepository;
    private $mockStorageUpgradePriceRepository;
    private $mockStorageUpgradeRepository;
    private $mockStorageCardPaymentRepository;

    protected function setUp(): void
    {
        $this->mockSettingsRepository = $this->createMock(SettingsRepository::class);
        $this->mockStorageRepository = $this->createMock(StorageRepository::class);
        $this->mockStorageUpgradePriceRepository = $this->createMock(StorageUpgradePriceRepository::class);
        $this->mockStorageUpgradeRepository = $this->createMock(StorageUpgradeRepository::class);
        $this->mockStorageCardPaymentRepository = $this->createMock(StorageCardPaymentRepository::class);

        $this->storageService = new StorageService(
            $this->mockSettingsRepository,
            $this->mockStorageRepository,
            $this->mockStorageUpgradePriceRepository,
            $this->mockStorageUpgradeRepository,
            $this->mockStorageCardPaymentRepository
        );
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

    public function testGetPrices()
    {
        $prices = [$this->createMock(StorageUpgradePrice::class)];
        $this->mockStorageUpgradePriceRepository->method('getAllBySize')->willReturn($prices);

        $result = $this->storageService->getPrices();
        $this->assertEquals($prices, $result);
    }

    public function testGetStorageCardPaymentBySession()
    {
        $storageCardPayment = $this->createMock(StorageCardPayment::class);
        $this->mockStorageCardPaymentRepository->method('findBySessionId')->willReturn($storageCardPayment);

        $result = $this->storageService->getStorageCardPaymentBySession('sessionId');
        $this->assertEquals($storageCardPayment, $result);
    }

    public function testGetPriceByIdSuccess()
    {
        $price = $this->createMock(StorageUpgradePrice::class);
        $this->mockStorageUpgradePriceRepository->method('find')->willReturn($price);

        $result = $this->storageService->getPriceById(1);
        $this->assertEquals($price, $result);
    }

    public function testGetPriceByIdThrowsNotFoundException()
    {
        $this->expectException(NotFoundException::class);
        $this->mockStorageUpgradePriceRepository->method('find')->willReturn(null);

        $this->storageService->getPriceById(1);
    }

    public function testCreateUpgradeSuccess()
    {
        $account = $this->createMock(Account::class);
        $storage = $this->createMock(Storage::class);
        $account->method('getStorage')->willReturn($storage);
        $storage->method('getMaxStorage')->willReturn(1000 * 1024 * 1024); // 1GB

        $metadata = $this->createMock(StoragePaymentMetadata::class);
        $metadata->method('getPriceCzk')->willReturn(1000);
        $metadata->method('getSize')->willReturn(500 * 1024 * 1024); // 500MB

        $storage->expects($this->once())->method('setMaxStorage')->with($this->equalTo(1500 * 1024 * 1024)); // 1.5GB

        $this->mockStorageCardPaymentRepository->method('findBySessionId')->willReturn(null);
        $this->mockStorageUpgradeRepository->expects($this->once())->method('save');

        $this->storageService->createUpgrade($account, $metadata, 'sessionId');
    }

    public function testCreateUpgradeThrowsConflictException()
    {
        $this->expectException(ConflictException::class);

        $storageCardPayment = $this->createMock(StorageCardPayment::class);
        $this->mockStorageCardPaymentRepository->method('findBySessionId')->willReturn($storageCardPayment);

        $this->storageService->createUpgrade(
            $this->createMock(Account::class),
            $this->createMock(StoragePaymentMetadata::class),
            'sessionId'
        );
    }

    public function testConvertSizeToBytes()
    {
        $reflection = new \ReflectionClass($this->storageService);
        $method = $reflection->getMethod('convertSizeToBytes');
        $method->setAccessible(true);

        $this->assertEquals(1024, $method->invokeArgs($this->storageService, ['1KB']));
        $this->assertEquals(1024 * 1024, $method->invokeArgs($this->storageService, ['1MB']));
        $this->assertEquals(1024 * 1024 * 1024, $method->invokeArgs($this->storageService, ['1GB']));
        $this->assertEquals(1024 * 1024 * 1024 * 1024, $method->invokeArgs($this->storageService, ['1TB']));
    }

    public function testHasExceededStorageLimit()
    {
        $reflection = new \ReflectionClass($this->storageService);
        $method = $reflection->getMethod('hasExceededStorageLimit');
        $method->setAccessible(true);

        $this->assertTrue($method->invokeArgs($this->storageService, [1024, 2048]));
        $this->assertFalse($method->invokeArgs($this->storageService, [2048, 1024]));
    }

    public function testHasExceededFileLimit()
    {
        $reflection = new \ReflectionClass($this->storageService);
        $method = $reflection->getMethod('hasExceededFileLimit');
        $method->setAccessible(true);

        $this->mockSettingsRepository->method('getMaxFileSize')->willReturn('500MB');
        $this->assertTrue($method->invokeArgs($this->storageService, [600 * 1024 * 1024]));
        $this->assertFalse($method->invokeArgs($this->storageService, [400 * 1024 * 1024]));
    }
}
