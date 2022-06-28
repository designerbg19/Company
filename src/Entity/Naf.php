<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\NafRepository;
use App\Controller\NafController;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=NafRepository::class)
 * @ApiResource(
 *     collectionOperations={"get","post",
 *      "deleteMultiple" = {
 *              "controller"=NafController::class,
 *              "method" = "DELETE",
 *              "path" = "/nafs"
 *     },},
 *     itemOperations={"get", "put"}
 * )
 */
class Naf
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
