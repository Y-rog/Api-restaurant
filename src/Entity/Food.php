<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\FoodRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: FoodRepository::class)]
class Food
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['food'])]
    private ?int $id = null;

    #[Groups(['food'])]
    #[ORM\Column(length: 64)]
    private ?string $title = null;

    #[Groups(['food'])]
    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[Groups(['food'])]
    #[ORM\Column(type: Types::FLOAT)]
    private ?int $price = null;

    #[Groups(['food'])]
    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[Groups(['food'])]
    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * @var Collection<int, FoodCategory>
     */
    #[ORM\OneToMany(targetEntity: FoodCategory::class, mappedBy: 'food', orphanRemoval: true)]
    private Collection $foodCategories;

    public function __construct()
    {
        $this->foodCategories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(int $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Collection<int, FoodCategory>
     */
    public function getFoodCategories(): Collection
    {
        return $this->foodCategories;
    }

    public function addFoodCategory(FoodCategory $foodCategory): static
    {
        if (!$this->foodCategories->contains($foodCategory)) {
            $this->foodCategories->add($foodCategory);
            $foodCategory->setFood($this);
        }

        return $this;
    }

    public function removeFoodCategory(FoodCategory $foodCategory): static
    {
        if ($this->foodCategories->removeElement($foodCategory)) {
            // set the owning side to null (unless already changed)
            if ($foodCategory->getFood() === $this) {
                $foodCategory->setFood(null);
            }
        }

        return $this;
    }
}
