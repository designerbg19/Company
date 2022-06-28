<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\NotationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=NotationRepository::class)
 * @ApiResource()
 */
class Notation
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $criterion;

    /**
     * @ORM\Column(type="text")
     */
    private $information;

    /**
     *
     * @ORM\ManyToMany(targetEntity="Profile")
     * @ORM\JoinTable(name="profile_notation",
     *      joinColumns={@ORM\JoinColumn(name="profile_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="notation_id", referencedColumnName="id")}
     *      )
     */
    private $profiles;

    /**
     * @ORM\OneToMany(targetEntity="NoteNotation", mappedBy="notation")
     */
    private $noteNotation;

    public function __construct()
    {
        $this->profiles = new ArrayCollection();
        $this->noteNotation = new ArrayCollection();
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

    public function getCriterion(): ?string
    {
        return $this->criterion;
    }

    public function setCriterion(string $criterion): self
    {
        $this->criterion = $criterion;

        return $this;
    }

    public function getInformation(): ?string
    {
        return $this->information;
    }

    public function setInformation(string $information): self
    {
        $this->information = $information;

        return $this;
    }

    /**
     * @param ArrayCollection $noteNotation
     */
    public function addNoteNotation(NoteNotation $notation)
    {
        if (!$this->noteNotation->contains($notation)) {
            $this->noteNotation[] = $notation;
        }
        return $this;
    }

    public function removeNoteNotation(Notation $notation): self
    {
        if ($this->noteNotation->contains($notation)) {
            $this->noteNotation->removeElement($notation);
        }
        return $this;
    }
}
