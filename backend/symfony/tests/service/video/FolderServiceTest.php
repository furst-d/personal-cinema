<?php

namespace App\Tests\Service\Video;

use App\Entity\Video\Folder;
use App\Entity\Account\Account;
use App\Exception\NotFoundException;
use App\Repository\Video\FolderRepository;
use App\Service\Video\FolderService;
use App\Service\Video\ShareService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class FolderServiceTest extends TestCase
{
    /**
     * @var FolderRepository|MockObject
     */
    private $folderRepository;

    /**
     * @var EntityManagerInterface|MockObject
     */
    private $em;

    /**
     * @var FolderService
     */
    private $folderService
    ;
    private $shareService;

    protected function setUp(): void
    {
        $this->folderRepository = $this->createMock(FolderRepository::class);
        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->shareService = $this->createMock(ShareService::class);
        $this->folderService = new FolderService($this->folderRepository, $this->em, $this->shareService);
    }

    public function testGetFolderByIdReturnsFolder(): void
    {
        $account = $this->createMock(Account::class);
        $folder = new Folder('Test Folder', $account);

        $this->folderRepository
            ->expects($this->once())
            ->method('find')
            ->with($this->equalTo(1))
            ->willReturn($folder);

        $result = $this->folderService->getFolderById(1);

        $this->assertInstanceOf(Folder::class, $result);
        $this->assertSame('Test Folder', $result->getName());
    }

    public function testGetFolderByIdThrowsNotFoundException(): void
    {
        $this->folderRepository
            ->expects($this->once())
            ->method('find')
            ->with($this->equalTo(1))
            ->willReturn(null);

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Folder not found');

        $this->folderService->getFolderById(1);
    }

    public function testCreateFolder()
    {
        $account = $this->createMock(Account::class);
        $name = 'New Folder';
        $folder = new Folder($name, $account);

        $this->folderRepository
            ->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf(Folder::class))
            ->willReturnCallback(function (Folder $folder) {
                $reflectionClass = new ReflectionClass($folder);
                $property = $reflectionClass->getProperty('id');
                $property->setAccessible(true);
                $property->setValue($folder, 1);
            });

        $result = $this->folderService->createFolder($account, $name, null);

        $this->assertInstanceOf(Folder::class, $result);
        $this->assertSame($name, $result->getName());

        $reflectionClass = new ReflectionClass($result);
        $property = $reflectionClass->getProperty('id');
        $property->setAccessible(true);
        $this->assertEquals(1, $property->getValue($result));
    }

    public function testUpdateFolder()
    {
        $account = $this->createMock(Account::class);
        $folder = new Folder('Old Name', $account);
        $newName = 'Updated Name';

        $this->folderRepository
            ->expects($this->once())
            ->method('save')
            ->with($folder);

        $this->folderService->updateFolder($account, $folder, $newName, null);

        $this->assertSame($newName, $folder->getName());
    }

    public function testDeleteFolder()
    {
        $folder = $this->createMock(Folder::class);

        $this->folderRepository
            ->expects($this->once())
            ->method('delete')
            ->with($folder);

        $this->folderService->deleteFolder($folder);
    }

    public function testGetAccountFolderByIdReturnsFolder()
    {
        $account = $this->createMock(Account::class);
        $folder = new Folder('Test Folder', $account);

        $this->folderRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['owner' => $account, 'id' => 1])
            ->willReturn($folder);

        $result = $this->folderService->getAccountFolderById($account, 1);

        $this->assertInstanceOf(Folder::class, $result);
        $this->assertSame('Test Folder', $result->getName());
    }

    public function testGetAccountFolderByIdThrowsNotFoundException()
    {
        $account = $this->createMock(Account::class);

        $this->folderRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['owner' => $account, 'id' => 1])
            ->willReturn(null);

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Folder not found');

        $this->folderService->getAccountFolderById($account, 1);
    }
}
