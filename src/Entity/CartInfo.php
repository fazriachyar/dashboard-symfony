<?php

namespace App\Entity;

use App\Repository\CartInfoRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CartInfoRepository::class)]
class CartInfo
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $customerId = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $addTime = null;

    #[ORM\Column(length: 255)]
    private ?string $action = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCustomerId(): ?int
    {
        return $this->customerId;
    }

    public function setCustomerId(int $customerId): self
    {
        $this->customerId = $customerId;

        return $this;
    }

    public function getAddTime(): ?\DateTimeInterface
    {
        return $this->addTime;
    }

    public function setAddTime(\DateTimeInterface $addTime): self
    {
        $this->addTime = $addTime;

        return $this;
    }

    public function getAction(): ?string
    {
        return $this->action;
    }

    public function setAction(string $action): self
    {
        $this->action = $action;

        return $this;
    }
}
