<?php

namespace App\Entity;

use App\Repository\IngredientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: IngredientRepository::class)]
class Ingredient
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(["recipe"])]
    private ?string $name = null;

    #[ORM\ManyToMany(targetEntity: Recipe::class, mappedBy: 'ingredient')]
    private Collection $recipes;

    #[ORM\ManyToMany(targetEntity: Measure::class, inversedBy: 'ingredients')]
    #[Groups(["recipe"])]
    private Collection $measure;

    public function __construct()
    {
        $this->recipes = new ArrayCollection();
        $this->measure = new ArrayCollection();
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

    /**
     * @return Collection<int, Recipe>
     */
    public function getRecipes(): Collection
    {
        return $this->recipes;
    }

    public function addRecipe(Recipe $recipe): static
    {
        if (!$this->recipes->contains($recipe)) {
            $this->recipes->add($recipe);
            $recipe->addIngredient($this);
        }

        return $this;
    }

    public function removeRecipe(Recipe $recipe): static
    {
        if ($this->recipes->removeElement($recipe)) {
            $recipe->removeIngredient($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Measure>
     */
    public function getMeasure(): Collection
    {
        return $this->measure;
    }

    public function addMeasure(Measure $measure): static
    {
        if (!$this->measure->contains($measure)) {
            $this->measure->add($measure);
        }

        return $this;
    }

    public function removeMeasure(Measure $measure): static
    {
        $this->measure->removeElement($measure);

        return $this;
    }
}
