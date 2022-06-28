<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\PriceRepository;
use App\Controller\PriceController;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PriceRepository::class)
 * @ApiResource(
 *     collectionOperations={"get","post",
 *      "delete" = {
 *              "controller"=PriceController::class,
 *              "method" = "DELETE",
 *              "path" = "/prices"
 *     }},
 *     itemOperations={"get", "put"}
 * )
 */
class Price
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $unitPrice;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $weekPricePublicitySearch;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $seatPrice;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $discountMonth;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $discount3Month;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $discount6Month;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $tva;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUnitPrice(): ?float
    {
        return $this->unitPrice;
    }

    public function setUnitPrice(?float $unitPrice): self
    {
        $this->unitPrice = $unitPrice;

        return $this;
    }

    public function getSeatPrice(): ?float
    {
        return $this->seatPrice;
    }

    public function setSeatPrice(?float $seatPrice): self
    {
        $this->seatPrice = $seatPrice;

        return $this;
    }

    public function getDiscountMonth(): ?int
    {
        return $this->discountMonth;
    }

    public function setDiscountMonth(?int $discountMonth): self
    {
        $this->discountMonth = $discountMonth;

        return $this;
    }

    public function getDiscount3Month(): ?int
    {
        return $this->discount3Month;
    }

    public function setDiscount3Month(?int $discount3Month): self
    {
        $this->discount3Month = $discount3Month;

        return $this;
    }

    public function getDiscount6Month(): ?int
    {
        return $this->discount6Month;
    }

    public function setDiscount6Month(?int $discount6Month): self
    {
        $this->discount6Month = $discount6Month;

        return $this;
    }

    public function getTva(): ?float
    {
        return $this->tva;
    }

    public function setTva(?float $tva): self
    {
        $this->tva = $tva;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * @param mixed $isActive
     */
    public function setIsActive($isActive): void
    {
        $this->isActive = $isActive;
    }

    /**
     * @return mixed
     */
    public function getWeekPricePublicitySearch()
    {
        return $this->weekPricePublicitySearch;
    }

    /**
     * @param mixed $weekPricePublicitySearch
     */
    public function setWeekPricePublicitySearch($weekPricePublicitySearch): void
    {
        $this->weekPricePublicitySearch = $weekPricePublicitySearch;
    }

}
