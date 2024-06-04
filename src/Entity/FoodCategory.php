<?php

namespace App\Entity;

use App\Repository\FoodCategoryRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FoodCategoryRepository::class)]
class FoodCategory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'foodCategories')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Category $category = null;

    #[ORM\ManyToOne(inversedBy: 'foodCategories')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Food $food = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCategory(): ?category
    {
        return $this->category;
    }

    public function setCategory(?category $categoryId): static
    {
        $this->category = $categoryId;

        return $this;
    }

    public function getFood(): ?food
    {
        return $this->food;
    }

    public function setFood(?food $food): static
    {
        $this->food = $food;

        return $this;
    }
}
