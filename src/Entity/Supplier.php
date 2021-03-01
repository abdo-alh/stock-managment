<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\SupplierRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=SupplierRepository::class)
 */
class Supplier
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=160)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=160)
     * @Assert\Regex(pattern="/^(05|06|07)\d{8}$/", message="رقم الهاتف غير صالح") 
     */
    private $phone;

    /**
     * @ORM\OneToMany(targetEntity=Purshase::class, mappedBy="supplier")
     */
    private $purshases;

    /**
     * @ORM\ManyToOne(targetEntity=City::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $city;

    public function __construct()
    {
        $this->purshases = new ArrayCollection();
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

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getCity(): ?City
    {
        return $this->city;
    }

    public function setCity(?City $city): self
    {
        $this->city = $city;

        return $this;
    }

    /**
     * @return Collection|Purshase[]
     */
    public function getPurshases(): Collection
    {
        return $this->purshases;
    }

    public function addPurshase(Purshase $purshase): self
    {
        if (!$this->purshases->contains($purshase)) {
            $this->purshases[] = $purshase;
            $purshase->setSupplier($this);
        }

        return $this;
    }

    public function removePurshase(Purshase $purshase): self
    {
        if ($this->purshases->removeElement($purshase)) {
            // set the owning side to null (unless already changed)
            if ($purshase->getSupplier() === $this) {
                $purshase->setSupplier(null);
            }
        }

        return $this;
    }
}
