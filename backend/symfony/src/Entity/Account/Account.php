<?php

namespace App\Entity\Account;

use App\Entity\Folder;
use App\Repository\Account\AccountRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Attribute\MaxDepth;

#[ORM\Entity(repositoryClass: AccountRepository::class)]
#[ORM\Table(name: '`account`')]
class Account implements UserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['serialize'])]
    private string $email;

    #[ORM\Column(length: 255)]
    private string $password;

    #[ORM\Column(length: 255)]
    private string $salt;

    #[ORM\Column]
    #[Groups(['serialize'])]
    private DateTimeImmutable $createdAt;

    #[ORM\Column]
    #[Groups(['serialize'])]
    private bool $isActive = false;

    #[ORM\Column]
    #[Groups(['serialize'])]
    private bool $isDeleted = false;

    /**
     * @var Collection<int, Role>
     */
    #[ORM\ManyToMany(targetEntity: Role::class, inversedBy: "users")]
    #[Groups(['serialize'])]
    private Collection $roles;

    /**
     * @var Collection<int, ApiToken>
     */
    #[ORM\OneToMany(targetEntity: ApiToken::class, mappedBy: 'account', orphanRemoval: true)]
    private Collection $apiTokens;

    /**
     * @var Collection<int, Folder>
     */
    #[ORM\OneToMany(targetEntity: Folder::class, mappedBy: 'owner', orphanRemoval: true)]
    private Collection $folders;

    /**
     * @param string $email
     * @param string $password
     * @param string $salt
     */
    public function __construct(string $email, string $password, string $salt)
    {
        $this->email = $email;
        $this->password = $password;
        $this->createdAt = new DateTimeImmutable();
        $this->salt = $salt;
        $this->roles = new ArrayCollection();
        $this->apiTokens = new ArrayCollection();
        $this->folders = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     * @return void
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * @return string|null
     */
    public function getSalt(): ?string
    {
        return $this->salt;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->isActive;
    }

    /**
     * @param bool $isActive
     * @return void
     */
    public function setActive(bool $isActive): void
    {
        $this->isActive = $isActive;
    }

    /**
     * @return bool|null
     */
    public function isDeleted(): ?bool
    {
        return $this->isDeleted;
    }

    /**
     * @param bool $isDeleted
     * @return void
     */
    public function setDeleted(bool $isDeleted): void
    {
        $this->isDeleted = $isDeleted;
    }

    /**
     * @return array
     */
    public function getRolesAsStringArray(): array
    {
        return $this->roles->map(function (Role $role) {
            return $role->getKeyName();
        })->toArray();
    }

    /**
     * @return array
     */
    public function getRoles(): array
    {
        return $this->getRolesAsStringArray();
    }

    /**
     * @param Role $role
     * @return void
     */
    public function addRole(Role $role): void
    {
        if (!$this->roles->contains($role)) {
            $this->roles[] = $role;
        }
    }

    /**
     * @param Role $role
     * @return void
     */
    public function removeRole(Role $role): void
    {
        $this->roles->removeElement($role);
    }

    /**
     * @return Collection<int, ApiToken>
     */
    public function getApiTokens(): Collection
    {
        return $this->apiTokens;
    }

    /**
     * @return void
     */
    public function eraseCredentials(): void {
        // There is no credentials to erase
    }

    /**
     * @return string
     */
    public function getUserIdentifier(): string
    {
        return $this->getEmail();
    }

    /**
     * @return Collection<int, Folder>
     */
    public function getFolders(): Collection
    {
        return $this->folders;
    }
}