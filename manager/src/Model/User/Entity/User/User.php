<?php

namespace App\Model\User\Entity\User;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;

class User
{
    private const STATUS_NEW = 'new';
    private const STATUS_WAIT = 'wait';
    private const STATUS_ACTIVE = 'active';
    /**
     * @var Id
     */
    private $id;
    /**
     * @var DateTimeImmutable
     */
    private $date;
    /**
     * @var Email
     */
    private $email;
    /**
     * @var string
     */
    private $passwordHash;
    /**
     * @var string
     */
    private $confirmationToken;
    /**
     * @var string
     */
    private $status;
    /**
     * @var Network[]|ArrayCollection
     */
    private $networks;
    /**
     * @var ResetToken
     */
    private $resetToken;

    public function __construct(
        Id $id,
        DateTimeImmutable $date
    )
    {
        $this->id = $id;
        $this->date = $date;
        $this->status = self::STATUS_NEW;
        $this->networks = new ArrayCollection();
    }

    public function signUpByEmail(Email $email, string $passwordHash, string $confirmationToken): void
    {
        if (!$this->isNew()) {
            throw new \Exception('This user is already registered');
        }

        $this->status = self::STATUS_WAIT;
        $this->email = $email;
        $this->passwordHash = $passwordHash;
        $this->confirmationToken = $confirmationToken;
    }

    public function signUpByNetwork(string $network, string $identity): void
    {
        if (!$this->isNew()) {
            throw new \Exception('This user is already registered');
        }

        $this->attachNetwork($network, $identity);
        $this->status = self::STATUS_ACTIVE;
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

    /**
     * @return bool
     */
    public function isNew(): bool
    {
        return $this->status === self::STATUS_NEW;
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
            throw new \Exception('User is not active');
        }

        if (!$this->email) {
            throw new \Exception('Email is not specified.');
        }
        if ($this->resetToken && !$this->resetToken->isExpiredToDate($date)) {
            throw new \Exception('Previous reset token is not expired.');
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
}
