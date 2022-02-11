<?php

namespace App\Entity;

use App\Repository\CompanyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CompanyRepository::class)]
class Company
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\Column(type: 'string', length: 255)]
    private $address;

    #[ORM\Column(type: 'string', length: 255)]
    private $postalCode;

    #[ORM\Column(type: 'string', length: 255)]
    private $city;

    #[ORM\Column(type: 'string', length: 255)]
    private $NIP;

    #[ORM\OneToMany(mappedBy: 'seller', targetEntity: Invoice::class)]
    private $soldInvoices;

    #[ORM\OneToMany(mappedBy: 'buyer', targetEntity: Invoice::class)]
    private $buyInvoices;

    public function __construct()
    {
        $this->soldInvoices = new ArrayCollection();
        $this->buyInvoices = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->name;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(string $postalCode): self
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getNIP(): ?string
    {
        return $this->NIP;
    }

    public function setNIP(string $NIP): self
    {
        $this->NIP = $NIP;

        return $this;
    }

    /**
     * @return Collection|Invoice[]
     */
    public function getSoldInvoices(): Collection
    {
        return $this->soldInvoices;
    }

    public function addSoldInvoice(Invoice $soldInvoice): self
    {
        if (!$this->soldInvoices->contains($soldInvoice)) {
            $this->soldInvoices[] = $soldInvoice;
            $soldInvoice->setSeller($this);
        }

        return $this;
    }

    public function removeSoldInvoice(Invoice $soldInvoice): self
    {
        if ($this->soldInvoices->removeElement($soldInvoice)) {
            // set the owning side to null (unless already changed)
            if ($soldInvoice->getSeller() === $this) {
                $soldInvoice->setSeller(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Invoice[]
     */
    public function getBuyInvoices(): Collection
    {
        return $this->buyInvoices;
    }

    public function addBuyInvoice(Invoice $buyInvoice): self
    {
        if (!$this->buyInvoices->contains($buyInvoice)) {
            $this->buyInvoices[] = $buyInvoice;
            $buyInvoice->setBuyer($this);
        }

        return $this;
    }

    public function removeBuyInvoice(Invoice $buyInvoice): self
    {
        if ($this->buyInvoices->removeElement($buyInvoice)) {
            // set the owning side to null (unless already changed)
            if ($buyInvoice->getBuyer() === $this) {
                $buyInvoice->setBuyer(null);
            }
        }

        return $this;
    }
}
