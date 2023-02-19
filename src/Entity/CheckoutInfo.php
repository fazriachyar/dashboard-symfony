<?php

namespace App\Entity;

use App\Repository\CheckoutInfoRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CheckoutInfoRepository::class)]
class CheckoutInfo
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $customerId = null;

    #[ORM\Column(length: 255)]
    private ?string $customerContact = null;

    #[ORM\Column(length: 255)]
    private ?string $customerAddress = null;

    #[ORM\Column(length: 255)]
    private ?string $action = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $addTime = null;

    #[ORM\Column]
    private ?int $cartInfoId = null;

    #[ORM\Column(length: 255)]
    private ?string $orderNote = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    #[ORM\Column(length: 255,nullable: True)]
    private ?string $orderId = null;

    #[ORM\Column(length: 255,nullable: True)]
    private ?string $PaymentCode = null;

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

    public function getCustomerContact(): ?string
    {
        return $this->customerContact;
    }

    public function setCustomerContact(string $customerContact): self
    {
        $this->customerContact = $customerContact;

        return $this;
    }

    public function getCustomerAddress(): ?string
    {
        return $this->customerAddress;
    }

    public function setCustomerAddress(string $customerAddress): self
    {
        $this->customerAddress = $customerAddress;

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

    public function getAddTime(): ?\DateTimeInterface
    {
        return $this->addTime;
    }

    public function setAddTime(\DateTimeInterface $addTime): self
    {
        $this->addTime = $addTime;

        return $this;
    }

    public function getCartInfoId(): ?int
    {
        return $this->cartInfoId;
    }

    public function setCartInfoId(int $cartInfoId): self
    {
        $this->cartInfoId = $cartInfoId;

        return $this;
    }

    public function getOrderNote(): ?string
    {
        return $this->orderNote;
    }

    public function setOrderNote(string $orderNote): self
    {
        $this->orderNote = $orderNote;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getOrderId(): ?string
    {
        return $this->orderId;
    }

    public function setOrderId(string $orderId): self
    {
        $this->orderId = $orderId;

        return $this;
    }

    public function getPaymentCode(): ?string
    {
        return $this->PaymentCode;
    }

    public function setPaymentCode(string $PaymentCode): self
    {
        $this->PaymentCode = $PaymentCode;

        return $this;
    }
}
