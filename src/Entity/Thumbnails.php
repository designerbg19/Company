<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\ThumbnailsRepository;
use App\Controller\PartnershipController;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ThumbnailsRepository::class)
 *  * @ApiResource(
 *     collectionOperations={
 *     "get" = {
 *              "method" = "GET",
 *              "path" = "/partnership"
 *          },
 *     "post" = {
 *              "method" = "POST",
 *              "path" = "/partnership.{_format}"
 *     },
 *     "deleteMultiple" = {
 *              "controller"=PartnershipController::class,
 *              "method" = "DELETE",
 *              "path" = "/partnerships"
 *     }
 *     },
 *     itemOperations={
 *     "get" = {
 *               "method" = "GET",
 *               "path" = "/partnership/{id}"
 *     },
 *     "getPartnershipCompany" = {
 *              "controller"=PartnershipController::class,
 *               "method" = "GET",
 *               "path" = "/partnershipByCompany/{id}"
 *     },
 *     }
 * )
 */
class Thumbnails
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50, nullable="true")
     */
    private $title;

    /**
     * @ORM\Column(type="text", nullable="true")
     */
    private $description;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Files", cascade={"persist", "remove"})
     */
    private $image;

    /**
     * @ORM\ManyToOne(targetEntity="Company", inversedBy="thumbnails")
     * @ORM\JoinColumn(name="company_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $company;

    /**
     * @ORM\ManyToOne(targetEntity="Company", inversedBy="thumbnails")
     * @ORM\JoinColumn(name="partnership_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $partnership;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title): void
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description): void
    {
        $this->description = $description;
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

    /**
     * @return mixed
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @param mixed $company
     */
    public function setCompany($company): void
    {
        $this->company = $company;
    }

    /**
     * @return mixed
     */
    public function getPartnership()
    {
        return $this->partnership;
    }

    /**
     * @param mixed $partnership
     */
    public function setPartnership($partnership): void
    {
        $this->partnership = $partnership;
    }

}