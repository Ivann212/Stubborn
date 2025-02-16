<?php

// src/Entity/Product.php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Repository\ProductRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?float $price = null;

    // Le nom de l'image du produit
    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $imageFilename = null;

    #[ORM\Column(type: 'boolean')]
    private bool $isFeatured = false; 

    // Relation OneToMany avec ProductSizeStock
    #[ORM\OneToMany(mappedBy: 'product', targetEntity: ProductSize::class, orphanRemoval: true)]
    private Collection $sizeStocks;

    public function __construct()
    {
        $this->sizeStocks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }
    public function isFeatured(): bool
    {
        return $this->isFeatured;
    }

    public function setisFeatured(bool $isFeatured): self
    {
        $this->isFeatured = $isFeatured;
        return $this;
    }

    // Getter et Setter pour l'image
    public function getImageFilename(): ?string
    {
        return $this->imageFilename;
    }

    public function setImageFilename(?string $imageFilename): self
    {
        $this->imageFilename = $imageFilename;

        return $this;
    }

    // Getter pour la relation avec ProductSizeStock
    public function getSizeStocks(): Collection
    {
        return $this->sizeStocks;
    }

    // Ajouter un stock pour une taille
    public function addSizeStock(ProductSize $sizeStock): self
    {
        if (!$this->sizeStocks->contains($sizeStock)) {
            $this->sizeStocks[] = $sizeStock;
            $sizeStock->setProduct($this);
        }

        return $this;
    }

    // Retirer un stock pour une taille
    public function removeSizeStock(ProductSize $sizeStock): self
    {
        if ($this->sizeStocks->removeElement($sizeStock)) {
            // Set the owning side to null (unless already changed)
            if ($sizeStock->getProduct() === $this) {
                $sizeStock->setProduct(null);
            }
        }

        return $this;
    }
}
