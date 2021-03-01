<?php

namespace App\Entity;

use App\Repository\PurshaseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PurshaseRepository::class)
 */
class Purshase
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Supplier::class, inversedBy="purshases")
     * @ORM\JoinColumn(nullable=false)
     */
    private $supplier;

    /**
     * @ORM\Column(type="date")
     */
    private $purshase_date;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $status;

    /**
     * @ORM\Column(type="float")
     */
    private $amount;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $note;

    /**
     * @ORM\OneToMany(targetEntity=PurshaseDetails::class, mappedBy="purshase", orphanRemoval=true)
     */
    private $purshaseDetails;

    public function __construct()
    {
        $this->products = new ArrayCollection();
        $this->purshaseDetails = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSupplier(): ?Supplier
    {
        return $this->supplier;
    }

    public function setSupplier(?Supplier $supplier): self
    {
        $this->supplier = $supplier;

        return $this;
    }

    public function getPurshaseDate(): ?\DateTimeInterface
    {
        return $this->purshase_date;
    }

    public function setPurshaseDate(\DateTimeInterface $purshase_date): self
    {
        $this->purshase_date = $purshase_date;

        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

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

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): self
    {
        $this->note = $note;

        return $this;
    }

    /**
     * @return Collection|PurshaseDetails[]
     */
    public function getPurshaseDetails(): Collection
    {
        return $this->purshaseDetails;
    }

    public function addPurshaseDetail(PurshaseDetails $purshaseDetail): self
    {
        if (!$this->purshaseDetails->contains($purshaseDetail)) {
            $this->purshaseDetails[] = $purshaseDetail;
            $purshaseDetail->setPurshase($this);
        }

        return $this;
    }

    public function removePurshaseDetail(PurshaseDetails $purshaseDetail): self
    {
        if ($this->purshaseDetails->removeElement($purshaseDetail)) {
            // set the owning side to null (unless already changed)
            if ($purshaseDetail->getPurshase() === $this) {
                $purshaseDetail->setPurshase(null);
            }
        }

        return $this;
    }
}
