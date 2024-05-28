<?php

namespace App\Entity;

use App\Repository\MenuCategoryRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MenuCategoryRepository::class)]
class MenuCategory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'menuCategories')]
    #[ORM\JoinColumn(nullable: false)]
    private ?menu $menuId = null;

    #[ORM\ManyToOne(inversedBy: 'menuCategories')]
    #[ORM\JoinColumn(nullable: false)]
    private ?category $categoryId = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMenuId(): ?menu
    {
        return $this->menuId;
    }

    public function setMenuId(?menu $menuId): static
    {
        $this->menuId = $menuId;

        return $this;
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
}
