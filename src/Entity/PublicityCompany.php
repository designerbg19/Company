<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Traits\TimeStampableTrait;
use App\Repository\PublicityCompanyRepository;
use App\Controller\PublicityCompanyController;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PublicityCompanyRepository::class)
 * @ORM\HasLifecycleCallbacks()
 * @ApiResource(
 *     collectionOperations={
 *     "get" = {
 *              "method" = "GET",
 *              "path" = "/publicityCompany"
 *          },
 *     "myPurchases"={
 *              "controller"=PublicityCompanyController::class,
 *              "path"="/myPurchases/{id}",
 *              "method"="GET",
 *         },
 *     "post" = {
 *              "method" = "POST",
 *              "path" = "/publicityCompany.{_format}"
 *     },
 *     "delete" = {
 *              "controller"=PublicityCompanyController::class,
 *              "method" = "DELETE",
 *              "path" = "/publicityCompany"
 *     }
 *     },
 *     itemOperations={
 *     "get" = {
 *               "method" = "GET",
 *               "path" = "/publicityCompany/{id}"
 *     },
 *     "getDevis" = {
 *              "controller"=PublicityCompanyController::class,
 *               "method" = "GET",
 *               "path" = "/getDevis/{id}"
 *     },
 *     "myPurchases_download" = {
 *              "controller"=PublicityCompanyController::class,
 *               "method" = "GET",
 *               "path" = "/myPurchases/download/{id}"
 *     },
 *     }
 * )
 */
class PublicityCompany
{
    use TimeStampableTrait;
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $period;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $status;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $priceHT;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $priceTTC;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $priceDiscount;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $visible;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $paymentReference;

    /**
     * @ORM\ManyToMany(targetEntity="Company", cascade={"persist"})
     * @ORM\JoinTable(name="pub_comp_company",
     *      joinColumns={@ORM\JoinColumn(name="publicity_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="company_id", referencedColumnName="id", onDelete="CASCADE")}
     *      )
     */
    private $companies;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity="PublicityFiles", mappedBy="publicityCompany",cascade={"persist"})
     */
    private $publicityFiles;

    public function __construct() {
        $this->publicityFiles = new ArrayCollection();
        $this->companies = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPeriod(): ?int
    {
        return $this->period;
    }

    public function setPeriod(?int $period): self
    {
        $this->period = $period;

        return $this;
    }

    public function getStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(?bool $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getPriceHT(): ?float
    {
        return $this->priceHT;
    }

    public function setPriceHT(?float $priceHT): self
    {
        $this->priceHT = $priceHT;

        return $this;
    }

    public function getPriceTTC(): ?float
    {
        return $this->priceTTC;
    }

    public function setPriceTTC(?float $priceTTC): self
    {
        $this->priceTTC = $priceTTC;

        return $this;
    }

    public function getPriceDiscount(): ?int
    {
        return $this->priceDiscount;
    }

    public function setPriceDiscount(?int $priceDiscount): self
    {
        $this->priceDiscount = $priceDiscount;

        return $this;
    }

    public function getVisible(): ?bool
    {
        return $this->visible;
    }

    public function setVisible(?bool $visible): self
    {
        $this->visible = $visible;

        return $this;
    }

    public function getPaymentReference(): ?string
    {
        return $this->paymentReference;
    }

    public function setPaymentReference(?string $paymentReference): self
    {
        $this->paymentReference = $paymentReference;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCompanies()
    {
        return $this->companies;
    }

    /**
     * @param mixed $companies
     */
    public function addCompanies(Company $companies)
    {
        if (!$this->companies->contains($companies)) {
            $this->companies[] = $companies;
        }
        return $this;
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
    public function getPublicityFiles()
    {
        return $this->publicityFiles;
    }

    /**
     * @param ArrayCollection $publicityFiles
     */
    public function addPublicityFiles(PublicityFiles $publicityFile)
    {
        if (!$this->publicityFiles->contains($publicityFile)) {
            $this->publicityFiles[] = $publicityFile;
        }
        return $this;
    }

    public function removePublicityFiles(PublicityFiles $publicityFile): self
    {
        if ($this->publicityFiles->contains($publicityFile)) {
            $this->publicityFiles->removeElement($publicityFile);
        }
        return $this;
    }

}
