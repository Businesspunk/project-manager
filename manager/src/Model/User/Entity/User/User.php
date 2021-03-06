<?php

namespace App\Model\User\Entity\User;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="user_users", uniqueConstraints={
 *     @ORM\UniqueConstraint(columns={"email"}),
 *     @ORM\UniqueConstraint(columns={"confirm_token"}),
 *     @ORM\UniqueConstraint(columns={"reset_token_token"})
 * })
 */
class User
{
    public const STATUS_ACTIVE = 'active';
    public const STATUS_WAIT = 'wait';
    public const STATUS_BLOCK = 'block';
    /**
     * @var Id
     * @ORM\Column (type="user_user_id")
     * @ORM\Id
     */
    private $id;
    /**
     * @var DateTimeImmutable
     * @ORM\Column (type="datetime_immutable")
     */
    private $date;
    /**
     * @var Email|null
     * @ORM\Column (type="user_user_email", nullable=true)
     */
    private $email;
    /**
     * @var Name|null
     * @ORM\Embedded(class="Name")
     */
    private $name;
    /**
     * @var string
     * @ORM\Column (type="string", nullable=true, name="password_hash")
     */
    private $passwordHash;
    /**
     * @var string|null
     * @ORM\Column (type="string", nullable=true, name="confirm_token")
     */
    private $confirmationToken;
    /**
     * @var string
     * @ORM\Column (type="string", length=16)
     */
    private $status;
    /**
     * @var Role
     * @ORM\Column (type="user_user_role")
     */
    private $role;
    /**
     * @var Network[]|ArrayCollection
     * @ORM\OneToMany(targetEntity="Network", mappedBy="user", orphanRemoval=true,cascade={"persist"})
     */
    private $networks;
    /**
     * @var ResetToken|null
     * @ORM\Embedded(class="ResetToken", columnPrefix="reset_token_")
     */
    private $resetToken;

    /**
     * @var Email|null
     * @ORM\Column (type="user_user_email", nullable=true)
     */
    private $newEmail;
    /**
     * @var string|null
     * @ORM\Column (type="string", nullable=true)
     */
    private $newEmailToken;

    private function __construct(
        Id $id,
        DateTimeImmutable $date,
        Name $name
    ) {
        $this->id = $id;
        $this->date = $date;
        $this->name = $name;
        $this->role = Role::user();
        $this->networks = new ArrayCollection();
    }

    public static function signUpByEmail(
        Id $id,
        DateTimeImmutable $date,
        Email $email,
        Name $name,
        string $passwordHash,
        ?string $confirmationToken
    ): self {
        $user = new self($id, $date, $name);
        $user->status = self::STATUS_WAIT;
        $user->email = $email;
        $user->passwordHash = $passwordHash;
        $user->confirmationToken = $confirmationToken;
        return $user;
    }

    public static function signUpByNetwork(
        Id $id,
        DateTimeImmutable $date,
        Name $name,
        string $network,
        string $identity
    ): self {
        $user = new self($id, $date, $name);
        $user->status = self::STATUS_ACTIVE;
        $user->attachNetwork($network, $identity);
        return $user;
    }

    public function getName(): ?Name
    {
        return $this->name;
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function getEmail(): ?Email
    {
        return $this->email;
    }

    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }

    public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }

    public function getToken(): ?string
    {
        return $this->confirmationToken;
    }

    public function getNetworks(): array
    {
        return $this->networks->toArray();
    }

    public function getResetToken(): ?ResetToken
    {
        return $this->resetToken;
    }

    public function getRole(): Role
    {
        return $this->role;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getNewEmail(): ?Email
    {
        return $this->newEmail;
    }

    public function getNewEmailToken(): ?string
    {
        return $this->newEmailToken;
    }

    public function isWait(): bool
    {
        return $this->status === self::STATUS_WAIT;
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function isBlocked(): bool
    {
        return $this->status === self::STATUS_BLOCK;
    }

    public function attachNetwork(string $network, string $identity): void
    {
        foreach ($this->networks as $existing) {
            if ($existing->isForNetwork($network)) {
                throw new \DomainException('This network is already attached.');
            }
        }
        $this->networks->add(new Network($this, $network, $identity));
    }

    public function detachNetwork(string $network, string $identity): void
    {
        foreach ($this->networks as $existing) {
            if ($existing->isFor($network, $identity)) {
                if (is_null($this->getEmail()) && $this->networks->count() === 1) {
                    throw new \DomainException('You can not detach last identity');
                }
                $this->networks->removeElement($existing);
                return;
            }
        }

        throw new \DomainException('This network is not exist');
    }

    public function confirmRegistration(): void
    {
        if (!$this->isWait()) {
            throw new \DomainException('User is already confirmed.');
        }

        $this->status = self::STATUS_ACTIVE;
        $this->confirmationToken = null;
    }

    public function requestResetPassword(ResetToken $token, DateTimeImmutable $date): void
    {
        if (!$this->isActive()) {
            throw new \DomainException('User is not active');
        }

        if (!$this->email) {
            throw new \DomainException('Email is not specified.');
        }
        if ($this->resetToken && !$this->resetToken->isExpiredToDate($date)) {
            throw new \DomainException('Previous reset token is not expired.');
        }

        $this->resetToken = $token;
    }

    public function resetPassword(DateTimeImmutable $date, string $hash): void
    {
        if ($this->resetToken === null) {
            throw new \DomainException('Reset password is not requested');
        }
        if ($this->resetToken->isExpiredToDate($date)) {
            throw new \DomainException('Reset token expired.');
        }

        $this->passwordHash = $hash;
    }

    public function changeRole(Role $role): void
    {
        if ($this->role->isEqual($role)) {
            throw new \DomainException('The role is already the same');
        }
        $this->role = $role;
    }
    /**
     * @ORM\PostLoad()
     */
    public function postLoad()
    {
        if ($this->resetToken->isEmpty()) {
            $this->resetToken = null;
        }
    }

    public function requestChangeEmail(Email $email, string $token)
    {
        if (!is_null($this->getEmail()) && $email->getValue() === $this->getEmail()->getValue()) {
            throw new \DomainException('Email is the same');
        }

        if (!$this->isActive()) {
            throw new \DomainException('User is not active');
        }

        $this->newEmail = $email;
        $this->newEmailToken = $token;
    }

    public function changeEmail(string $token): void
    {
        if (is_null($this->newEmailToken) || is_null($this->newEmail)) {
            throw new \DomainException('Change email was not requested');
        }

        if ($token != $this->newEmailToken) {
            throw new \DomainException('Token is invalid');
        }

        $this->email = $this->newEmail;
        $this->newEmailToken = null;
        $this->newEmail = null;
    }

    public function changeName(Name $name): void
    {
        $this->name = $name;
    }

    public function edit(Email $email, Name $name): void
    {
        $this->email = $email;
        $this->changeName($name);
    }

    public function activate(): void
    {
        if ($this->isActive()) {
            throw new \DomainException('User is already active');
        }

        $this->status = self::STATUS_ACTIVE;
    }

    public function block(): void
    {
        if ($this->isBlocked()) {
            throw new \DomainException('User is already blocked');
        }

        $this->status = self::STATUS_BLOCK;
    }
}
