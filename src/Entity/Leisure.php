<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\LeisureRepository;
use App\Controller\LeisureController;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource(
 *      collectionOperations={"get","post",
 *      "deleteMultiple" = {
 *              "controller"=LeisureController::class,
 *              "method" = "DELETE",
 *              "path" = "/leisures"
 *     },},
 *     itemOperations={"get",
 *     }
 * )
 * @ORM\Entity(repositoryClass=LeisureRepository::class)
 */
class Leisure
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
}
