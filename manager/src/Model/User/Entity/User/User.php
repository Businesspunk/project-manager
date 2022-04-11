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
    public const STATUS_WAIT = 'wait';
    public const STATUS_ACTIVE = 'active';
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

    private function __construct(
        Id $id,
        DateTimeImmutable $date
    )
    {
        $this->id = $id;
        $this->date = $date;
        $this->role = Role::user();
        $this->networks = new ArrayCollection();
    }

    public static function signUpByEmail(
        Id $id, DateTimeImmutable $date, Email $email, string $passwordHash, string $confirmationToken): self
    {
        $user = new self($id, $date);
        $user->status = self::STATUS_WAIT;
        $user->email = $email;
        $user->passwordHash = $passwordHash;
        $user->confirmationToken = $confirmationToken;
        return $user;
    }

    public static function signUpByNetwork(Id $id, DateTimeImmutable $date, string $network, string $identity): self
    {
        $user = new self($id, $date);
        $user->status = self::STATUS_ACTIVE;
        $user->attachNetwork($network, $identity);
        return $user;
    }

    private function attachNetwork(string $network, string $identity): void
    {
        foreach ($this->networks as $existing) {
            if ($existing->isForNetwork($network)) {
                throw new \Exception('This network is already attached.');
            }
        }
        $this->networks->add(new Network($this, $network, $identity));
    }

    /**
     * @return Id
     */
    public function getId(): Id
    {
        return $this->id;
    }

    /**
     * @return Email
     */
    public function getEmail(): Email
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }

    /**
     * @return string
     */
    public function getToken(): ?string
    {
        return $this->confirmationToken;
    }

    /**
     * @return bool
     */
    public function isWait(): bool
    {
        return $this->status === self::STATUS_WAIT;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function confirmRegistration(): void
    {
        if (!$this->isWait()) {
            throw new \DomainException('User is already confirmed.');
        }

        $this->status = self::STATUS_ACTIVE;
        $this->confirmationToken = null;
    }

    /**
     * @return array
     */
    public function getNetworks(): array
    {
        return $this->networks->toArray();
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

    /**
     * @return ResetToken
     */
    public function getResetToken(): ?ResetToken
    {
        return $this->resetToken;
    }

    public function resetPassword(DateTimeImmutable $date, string $hash): void
    {
        if ($this->resetToken === null) {
            throw new \Exception('Reset password is not requested');
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

    public function getRole(): Role
    {
        return $this->role;
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
}
