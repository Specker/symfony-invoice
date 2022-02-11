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

    #[ORM\OneToMany(mappedBy: 'invoice_id', targetEntity: InvoiceProduct::class, cascade: ["persist", "remove"])]
    private $invoiceProducts;


    public function __construct()
    {
        $this->invoiceProducts = new ArrayCollection();
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

    /**
     * @return Collection|InvoiceProduct[]
     */
    public function getInvoiceProducts(): Collection
    {
        return $this->invoiceProducts;
    }

    public function addInvoiceProduct(InvoiceProduct $invoiceProduct): self
    {
        if (!$this->invoiceProducts->contains($invoiceProduct)) {
            $this->invoiceProducts[] = $invoiceProduct;
            $invoiceProduct->setInvoiceId($this);
        }

        return $this;
    }

    public function removeInvoiceProduct(InvoiceProduct $invoiceProduct): self
    {
        if ($this->invoiceProducts->removeElement($invoiceProduct)) {
            // set the owning side to null (unless already changed)
            if ($invoiceProduct->getInvoiceId() === $this) {
                $invoiceProduct->setInvoiceId(null);
            }
        }

        return $this;
    }


}
