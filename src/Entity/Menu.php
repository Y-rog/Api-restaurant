<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\MenuRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: MenuRepository::class)]
class Menu
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['menu'])]
    private ?int $id = null;

    #[Groups(['menu'])]
    #[ORM\Column(length: 64)]
    private ?string $title = null;

    #[Groups(['menu'])]
    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[Groups(['menu'])]
    #[ORM\Column(type: Types::FLOAT)]
    private ?int $price = null;

    #[Groups(['menu'])]
    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[Groups(['menu'])]
    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'menus', targetEntity: Restaurant::class, cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Restaurant $restaurant = null;

    #[ORM\OneToMany(targetEntity: MenuCategory::class, mappedBy: 'menu', orphanRemoval: true)]
    private Collection $menuCategories;


    public function __construct()
    {
        $this->menuCategories = new ArrayCollection();
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

    public function getRestaurant(): ?restaurant
    {
        return $this->restaurant;
    }

    public function setRestaurant(?restaurant $restaurant): static
    {
        $this->restaurant = $restaurant;

        return $this;
    }

    public function getMenuCategories(): Collection
    {
        return $this->menuCategories;
    }

    public function addMenuCategory(MenuCategory $menuCategory): static
    {
        if (!$this->menuCategories->contains($menuCategory)) {
            $this->menuCategories->add($menuCategory);
            $menuCategory->setMenu($this);
        }

        return $this;
    }

    public function removeMenuCategory(MenuCategory $menuCategory): static
    {
        if ($this->menuCategories->removeElement($menuCategory)) {
            // set the owning side to null (unless already changed)
            if ($menuCategory->getMenu() === $this) {
                $menuCategory->setMenu(null);
            }
        }

        return $this;
    }
}
