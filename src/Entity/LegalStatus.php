<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\LegalStatusRepository;
use App\Controller\LegalStatusController;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=LegalStatusRepository::class)
 * @ApiResource(
 *     collectionOperations={
 *     "get" = {
 *              "method" = "GET",
 *              "path" = "/legalStatus"
 *          },
 *     "post" = {
 *              "method" = "POST",
 *              "path" = "/legalStatus.{_format}"
 *     },
 *     "delete" = {
 *              "controller"=LegalStatusController::class,
 *              "method" = "DELETE",
 *              "path" = "/legalStatus"
 *     }
 *     },
 *     itemOperations={
 *     "get" = {
 *               "method" = "GET",
 *               "path" = "/legalStatus/{id}"
 *     },
 *     "put" = {
 *               "method" = "PUT",
 *               "path" = "/legalStatus/{id}"
 *     },
 *     }
 * )
 */
class LegalStatus
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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $country;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }
    /**
     * @return mixed
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param mixed $country
     */
    public function setCountry($country): void
    {
        $this->country = $country;
    }
}
