<?php

namespace App\Service\Video;

use App\DTO\PaginatorRequest;
use App\Entity\Account\Account;
use App\Entity\Video\Folder;
use App\Exception\BadRequestException;
use App\Exception\NotFoundException;
use App\Helper\DTO\PaginatorResult;
use App\Helper\Video\FolderData;
use App\Repository\Video\FolderRepository;
use Doctrine\ORM\EntityManagerInterface;

class FolderService
{
    /**
     * @var FolderRepository $folderRepository
     */
    private FolderRepository $folderRepository;

    /**
     * @var ShareService $shareService
     */
    private ShareService $shareService;

    /**
     * @var EntityManagerInterface $em
     */
    private EntityManagerInterface $em;

    private const NOT_FOUND_MESSAGE = 'Folder not found';

    /**
     * @param FolderRepository $folderRepository
     * @param EntityManagerInterface $em
     * @param ShareService $shareService
     */
    public function __construct(
        FolderRepository $folderRepository,
        EntityManagerInterface $em,
        ShareService $shareService
    )
    {
        $this->folderRepository = $folderRepository;
        $this->em = $em;
        $this->shareService = $shareService;
    }

    /**
     * @param int $id
     * @return Folder|null
     * @throws NotFoundException
     */
    public function getFolderById(int $id): ?Folder
    {
        $folder = $this->folderRepository->find($id);

        if (!$folder) {
            throw new NotFoundException(self::NOT_FOUND_MESSAGE);
        }

        return $folder;
    }

    /**
     * @param Account $account
     * @param int $id
     * @return Folder
     * @throws NotFoundException
     */
    public function getAccountFolderById(Account $account, int $id): Folder
    {
        $folder = $this->folderRepository->findOneBy(['id' => $id, 'owner' => $account]);

        if (!$folder) {
            throw new NotFoundException(self::NOT_FOUND_MESSAGE);
        }

        return $folder;
    }

    /**
     * @param Account $account
     * @param int|null $id
     * @return FolderData
     * @throws NotFoundException
     */
    public function getAccountFolderDataById(Account $account, ?int $id): FolderData
    {
        if (is_null($id)) {
            return new FolderData(null, false);
        }

        if ($id === 0) {
            return new FolderData(null, true);
        }

        return new FolderData($this->getAccountFolderById($account, $id), false);
    }

    /**
     * @param Account $account
     * @param int|null $id
     * @return FolderData
     * @throws NotFoundException
     */
    public function getSharedFolderDataById(Account $account, ?int $id): FolderData
    {
        if (is_null($id)) {
            return new FolderData(null, false);
        }

        if ($id === 0) {
            return new FolderData(null, true);
        }

        return new FolderData($this->getSharedFolderById($account, $id), false);
    }

    /**
     * @param Account $account
     * @param int $id
     * @return Folder
     * @throws NotFoundException
     */
    public function getSharedFolderById(Account $account, int $id): Folder
    {
        $folder = $this->folderRepository->find($id);

        if (!$folder || !$this->shareService->hasSharedFolderAccess($account, $folder)) {
            throw new NotFoundException(self::NOT_FOUND_MESSAGE);
        }

        return $folder;
    }

    /**
     * @param Account $account
     * @param string $name
     * @param int|null $parentId
     * @return Folder
     * @throws NotFoundException
     */
    public function createFolder(Account $account, string $name, ?int $parentId): Folder
    {
        $parent = $parentId ? $this->getAccountFolderById($account, $parentId) : null;
        $folder = new Folder($name, $account, $parent);
        $this->folderRepository->save($folder);
        return $folder;
    }

    /**
     * @param Account $account
     * @param Folder $folder
     * @param string $name
     * @param int|null $parentId
     * @return void
     * @throws NotFoundException|BadRequestException
     */
    public function updateFolder(Account $account, Folder $folder, string $name, ?int $parentId): void
    {
        $parent = $parentId ? $this->getAccountFolderById($account, $parentId) : null;

        if ($parent && $this->isDescendant($parent, $folder)) {
            throw new BadRequestException("Cannot set a descendant folder as the parent.");
        }

        $folder->setName($name);
        $folder->setParent($parent);
        $this->folderRepository->save($folder);
    }

    /**
     * @param Folder $folder
     * @return void
     */
    public function deleteFolder(Folder $folder): void
    {
        $this->folderRepository->delete($folder);
    }

    /**
     * @param Account $account
     * @param FolderData $folderData
     * @param PaginatorRequest $paginatorRequest
     * @return PaginatorResult
     */
    public function getFolders(Account $account, FolderData $folderData, PaginatorRequest $paginatorRequest): PaginatorResult
    {
        return $this->folderRepository->findAccountFolders($account, $folderData, $paginatorRequest);
    }

    /**
     * @param Folder $folder
     * @return void
     */
    private function deleteRecursive(Folder $folder): void
    {
        // Recursively delete subfolders
        foreach ($folder->getSubFolders() as $subFolder) {
            $this->deleteRecursive($subFolder);
        }

        // Delete or nullify all videos in the folder
        foreach ($folder->getVideos() as $video) {
            $this->em->remove($video);
        }

        // Finally, remove the folder itself
        $this->em->remove($folder);
    }

    /**
     * Recursively check if $folder is a descendant of $potentialParent
     *
     * @param Folder $potentialParent
     * @param Folder $folder
     * @return bool
     */
    private function isDescendant(Folder $potentialParent, Folder $folder): bool
    {
        if ($potentialParent === $folder) {
            return true;
        }

        $current = $potentialParent->getParent();
        while ($current !== null) {
            if ($current === $folder) {
                return true;
            }
            $current = $current->getParent();
        }

        return false;
    }
}
