<?php

namespace App\Service\Video;

use App\DTO\PaginatorRequest;
use App\Entity\Account\Account;
use App\Entity\Video\Folder;
use App\Exception\BadRequestException;
use App\Exception\NotFoundException;
use App\Helper\DTO\PaginatorResult;
use App\Helper\Video\FolderData;
use App\Helper\Video\NameNormalizer;
use App\Repository\Video\FolderRepository;
use Doctrine\ORM\EntityManagerInterface;

class FolderService
{
    /**
     * @var FolderRepository $folderRepository
     */
    private FolderRepository $folderRepository;

    public const NOT_FOUND_MESSAGE = 'Folder not found';

    /**
     * @param FolderRepository $folderRepository
     */
    public function __construct(FolderRepository $folderRepository)
    {
        $this->folderRepository = $folderRepository;
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
     * @param int $id
     * @return Folder
     * @throws NotFoundException
     */
    public function getAccountAllowedFolderById(Account $account, int $id): Folder
    {
        $folder = $this->folderRepository->find($id);

        if (!$folder || !$this->hasUserAccessToFolder($account, $folder)) {
            throw new NotFoundException(self::NOT_FOUND_MESSAGE);
        }

        return $folder;
    }

    /**
     * @param Account $account
     * @param Folder $folder
     * @return bool
     */
    public function hasUserAccessToFolder(Account $account, Folder $folder): bool
    {
        while ($folder) {
            if ($folder->getOwner() === $account) {
                return true;
            }

            // Check if the folder is shared with the user
            foreach ($folder->getShares() as $share) {
                if ($share->getAccount() === $account) {
                    return true;
                }
            }

            $folder = $folder->getParent();
        }

        return false;
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

        return new FolderData($this->getAccountAllowedFolderById($account, $id), false);
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
        return $this->folderRepository->findFolders($account, $folderData, $paginatorRequest);
    }

    /**
     * @param Account $account
     * @param string $phrase
     * @param PaginatorRequest $paginatorRequest
     * @return PaginatorResult<Folder>
     */
    public function searchFolders(Account $account, string $phrase, PaginatorRequest $paginatorRequest): PaginatorResult
    {
        $phrase = NameNormalizer::normalize($phrase);
        return $this->folderRepository->searchFolders($account, $phrase, $paginatorRequest);
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
