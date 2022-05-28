<?php

namespace App\Entity;

use App\Repository\ProgramRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProgramRepository::class)]
#[UniqueEntity(
    fields: 'title',
    message: 'Cette série existe déjà.'
)]
class Program
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    #[Assert\NotBlank(message: 'Merci de rentrer un titre valide.')]
    #[Assert\Length(
        max: 255,
        maxMessage: "La série  saisie {{ value }} est trop longue et ne doit pas dépasser {{ limit }} caractères"
    )]
    private $title;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank(message: 'Veuillez entre un synopsis pour cette série.')]
    private $synopsis;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message: 'Veuillez insérer une image pour cette série')]
    #[Assert\Url(
        message: 'The Url {{ value }} n\'est pas une url valide' 
    )]
    private $poster;

    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'programs')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank(message: 'Veuillez sélectionner une catégorie.')]
    private $category;

    #[ORM\OneToMany(mappedBy: 'program', targetEntity: Season::class)]
    private $seasons;

    public function __construct()
    {
        $this->seasons = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getSynopsis(): ?string
    {
        return $this->synopsis;
    }

    public function setSynopsis(string $synopsis): self
    {
        $this->synopsis = $synopsis;

        return $this;
    }

    public function getPoster(): ?string
    {
        return $this->poster;
    }

    public function setPoster(?string $poster): self
    {
        $this->poster = $poster;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection<int, Season>
     */
    public function getSeasons(): Collection
    {
        return $this->seasons;
    }

    public function addSeason(Season $season): self
    {
        if (!$this->seasons->contains($season)) {
            $this->seasons[] = $season;
            $season->setProgram($this);
        }

        return $this;
    }

    public function removeSeason(Season $season): self
    {
        if ($this->seasons->removeElement($season)) {
            // set the owning side to null (unless already changed)
            if ($season->getProgram() === $this) {
                $season->setProgram(null);
            }
        }

        return $this;
    }
}
