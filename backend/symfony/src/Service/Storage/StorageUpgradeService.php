<?php

namespace App\Service\Storage;

use App\DTO\Filter\EmailFilterRequest;
use App\DTO\Filter\FilterRequest;
use App\DTO\PaginatorRequest;
use App\DTO\Storage\StorageUpgradeQueryRequest;
use App\Entity\Account\Account;
use App\Entity\Storage\StorageCardPayment;
use App\Entity\Storage\StorageUpgrade;
use App\Exception\ConflictException;
use App\Exception\InternalException;
use App\Exception\NotFoundException;
use App\Helper\DTO\PaginatorResult;
use App\Helper\Storage\StoragePaymentMetadata;
use App\Helper\Storage\StoragePaymentType;
use App\Repository\Storage\StorageCardPaymentRepository;
use App\Repository\Storage\StorageUpgradeRepository;
use App\Service\Payment\PaymentService;

class StorageUpgradeService
{
    /**
     * @var StorageUpgradeRepository $storageUpgradeRepository
     */
    private StorageUpgradeRepository $storageUpgradeRepository;

    /**
     * @var StorageCardPaymentRepository $storageCardPaymentRepository
     */
    private StorageCardPaymentRepository $storageCardPaymentRepository;

    /**
     * @var PaymentService $paymentService
     */
    private PaymentService $paymentService;

    /**
     * @param StorageUpgradeRepository $storageUpgradeRepository
     * @param StorageCardPaymentRepository $storageCardPaymentRepository
     * @param PaymentService $paymentService
     */
    public function __construct(
        StorageUpgradeRepository $storageUpgradeRepository,
        StorageCardPaymentRepository $storageCardPaymentRepository,
        PaymentService $paymentService
    )
    {
        $this->storageUpgradeRepository = $storageUpgradeRepository;
        $this->storageCardPaymentRepository = $storageCardPaymentRepository;
        $this->paymentService = $paymentService;
    }

    /**
     * @param PaginatorRequest $paginatorRequest
     * @param FilterRequest|null $filterRequest
     * @return PaginatorResult<StorageUpgrade>
     */
    public function getUpgrades(PaginatorRequest $paginatorRequest, ?FilterRequest $filterRequest): PaginatorResult
    {
        return $this->storageUpgradeRepository->getUpgrades($paginatorRequest, $filterRequest);
    }

    /**
     * @param StoragePaymentMetadata $metadata
     * @return StorageUpgrade
     * @throws ConflictException
     * @throws InternalException
     */
    public function createUpgrade(StoragePaymentMetadata $metadata): StorageUpgrade
    {
        if ($metadata->getPaymentIntent()
            && ($this->getStorageCardPaymentByPaymentIntent($metadata->getPaymentIntent())
            || $this->paymentService->isRefunded($metadata->getPaymentIntent()))
        ) {
            throw new ConflictException('Upgrade already exists');
        }

        $storageUpgrade = new StorageUpgrade(
            $metadata->getAccount(),
            $metadata->getPriceCzk(),
            $metadata->getSize(),
            $metadata->getType(),
            $metadata->getPaymentIntent()
        );

        $storage = $metadata->getAccount()->getStorage();
        $storage->setMaxStorage($storage->getMaxStorage() + $metadata->getSize());

        $this->storageUpgradeRepository->save($storageUpgrade);

        return $storageUpgrade;
    }

    /**
     * @param string $paymentIntent
     * @return StorageCardPayment|null
     */
    public function getStorageCardPaymentByPaymentIntent(string $paymentIntent): ?StorageCardPayment
    {
        return $this->storageCardPaymentRepository->findByPaymentIntent($paymentIntent);
    }

    /**
     * @param int[] $ids
     * @return StorageUpgrade[]
     * @throws NotFoundException
     */
    public function getUpgradesByIds(array $ids): array
    {
        $upgrades = $this->storageUpgradeRepository->findByIds($ids);

        if (count($upgrades) !== count($ids)) {
            throw new NotFoundException('Some upgrades not found.');
        }

        return $upgrades;
    }

    /**
     * @param StorageUpgrade $upgrade
     * @return void
     * @throws InternalException
     */
    public function deleteUpgrade(StorageUpgrade $upgrade): void
    {
        $storage = $upgrade->getAccount()->getStorage();
        $storage->setMaxStorage($storage->getMaxStorage() - $upgrade->getSize());

        if ($upgrade->getPaymentType() === StoragePaymentType::CARD) {
            $this->paymentService->cancelPayment($upgrade->getStorageCardPayment()->getPaymentIntent());
        }

        $this->storageUpgradeRepository->delete($upgrade);
    }
}
