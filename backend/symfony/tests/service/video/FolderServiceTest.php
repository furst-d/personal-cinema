<?php

namespace App\Tests\Service\Video;

use App\Entity\Video\Folder;
use App\Entity\Account\Account;
use App\Exception\NotFoundException;
use App\Repository\Video\FolderRepository;
use App\Service\Video\FolderService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class FolderServiceTest extends TestCase
{
    /**
     * @var FolderRepository|MockObject
     */
    private $folderRepository;

    /**
     * @var FolderService
     */
    private $folderService;

    protected function setUp(): void
    {
        $this->folderRepository = $this->createMock(FolderRepository::class);
        $this->folderService = new FolderService($this->folderRepository);
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
}
