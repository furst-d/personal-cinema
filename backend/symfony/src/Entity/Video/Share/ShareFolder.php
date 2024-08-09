<?php

namespace App\Entity\Video\Share;

use App\Entity\Account\Account;
use App\Entity\Video\Folder;
use App\Repository\Video\Share\ShareFolderRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ShareFolderRepository::class)]
class ShareFolder
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'shares')]
    #[ORM\JoinColumn(nullable: false)]
    private Folder $folder;

    #[ORM\ManyToOne(inversedBy: 'sharedVideos')]
    #[ORM\JoinColumn(nullable: false)]
    private Account $account;

    #[ORM\Column]
    private DateTimeImmutable $createdAt;

    /**
     * @param Folder $folder
     * @param Account $account
     */
    public function __construct(Folder $folder, Account $account)
    {
        $this->folder = $folder;
        $this->account = $account;
        $this->createdAt = new DateTimeImmutable();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return Folder
     */
    public function getFolder(): Folder
    {
        return $this->folder;
    }

    /**
     * @return Account
     */
    public function getAccount(): Account
    {
        return $this->account;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
}
