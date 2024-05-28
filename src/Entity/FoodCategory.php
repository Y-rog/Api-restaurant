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
    private ?category $categoryId = null;

    #[ORM\ManyToOne(inversedBy: 'foodCategories')]
    #[ORM\JoinColumn(nullable: false)]
    private ?food $foodId = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCategoryId(): ?category
    {
        return $this->categoryId;
    }

    public function setCategoryId(?category $categoryId): static
    {
        $this->categoryId = $categoryId;

        return $this;
    }

    public function getFoodId(): ?food
    {
        return $this->foodId;
    }

    public function setFoodId(?food $foodId): static
    {
        $this->foodId = $foodId;

        return $this;
    }
}
