<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\PublicityRepository;
use App\Controller\PublicityController;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PublicityRepository::class)
 * @ApiResource(
 *     collectionOperations={"get","post",
 *      "delete" = {
 *              "controller"=PublicityController::class,
 *              "method" = "DELETE",
 *              "path" = "/publicities"
 *     },
 *     "getPurchases" = {
 *              "controller"=PublicityController::class,
 *               "method" = "GET",
 *               "path" = "/getPurchasesByUser/{id}"
 *     },},
 *     itemOperations={"get"}
 * )
 */
class Publicity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $links;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $startDay;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $endDay;

    /**
     * @ORM\Column(type="boolean")
     */
    private $status;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Files", cascade={"persist", "remove"})
     */
    private $image;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Files", cascade={"persist", "remove"})
     */
    private $pdf;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $price;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLinks(): ?string
    {
        return $this->links;
    }

    public function setLinks(string $links): self
    {
        $this->links = $links;

        return $this;
    }

    public function getStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): self
    {
        $this->status = $status;

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

    /**
     * @return mixed
     */
    public function getPdf()
    {
        return $this->pdf;
    }

    /**
     * @param mixed $pdf
     */
    public function setPdf($pdf): void
    {
        $this->pdf = $pdf;
    }

    /**
     * @return mixed
     */
    public function getStartDay()
    {
        return $this->startDay;
    }

    /**
     * @param mixed $startDay
     */
    public function setStartDay($startDay): void
    {
        $this->startDay = $startDay;
    }

    /**
     * @return mixed
     */
    public function getEndDay()
    {
        return $this->endDay;
    }

    /**
     * @param mixed $endDay
     */
    public function setEndDay($endDay): void
    {
        $this->endDay = $endDay;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user): void
    {
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param mixed $price
     */
    public function setPrice($price): void
    {
        $this->price = $price;
    }

}
