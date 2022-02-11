<?php

namespace App\Entity;

use App\Repository\InvoiceRepository;
use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InvoiceRepository::class)]
class Invoice
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string',length: 255)]
    private $date;

    #[ORM\ManyToOne(targetEntity: Company::class, inversedBy: 'invoices')]
    #[ORM\JoinColumn(nullable: false)]
    private $seller;

    #[ORM\ManyToOne(targetEntity: Company::class, inversedBy: 'invoices')]
    #[ORM\JoinColumn(nullable: false)]
    private $buyer;

    #[ORM\Column(type: 'float',length: 255)]
    private $totalPrice;


    public function __construct()
    {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?string
    {
        return $this->date;
    }

    public function setDate(string $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getSeller(): ?Company
    {
        return $this->seller;
    }

    public function setSeller(?Company $seller): self
    {
        $this->seller = $seller;

        return $this;
    }

    public function getBuyer(): ?Company
    {
        return $this->buyer;
    }

    public function setBuyer(?Company $buyer): self
    {
        $this->buyer = $buyer;

        return $this;
    }

    public function getTotalPrice(): ?float
    {
        return $this->totalPrice;
    }

    public function setTotalPrice(float $totalPrice): self
    {
        $this->totalPrice = $totalPrice;

        return $this;
    }


}
