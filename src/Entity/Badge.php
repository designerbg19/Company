<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\BadgeRepository;
use App\Controller\BadgeController;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=BadgeRepository::class)
 * @ApiResource(
 *     collectionOperations={"get","post",
 *      "deleteMultiple" = {
 *              "controller"=BadgeController::class,
 *              "method" = "DELETE",
 *              "path" = "/badges"
 *     },
 *     },
 *     itemOperations={"get"}
 * )
 */
class Badge
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
     * @ORM\Column(type="boolean")
     */
    private $published;

    /**
     * @ORM\Column(type="integer",nullable=true)
     */
    private $seuilBas;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Files", cascade={"persist", "remove"})
     */
    private $image;

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

    public function getPublished(): ?bool
    {
        return $this->published;
    }

    public function setPublished(bool $published): self
    {
        $this->published = $published;

        return $this;
    }

    public function getSeuilBas(): ?int
    {
        return $this->seuilBas;
    }

    public function setSeuilBas(int $seuilBas): self
    {
        $this->seuilBas = $seuilBas;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param mixed $image
     */
    public function setImage($image): void
    {
        $this->image = $image;
    }
}
