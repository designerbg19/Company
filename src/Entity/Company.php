<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Traits\TimeStampableTrait;
use App\Repository\CompanyRepository;
use App\Controller\CompanyController;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CompanyRepository::class)
 * @ORM\HasLifecycleCallbacks()
 * @ApiResource(
 *     collectionOperations={"get","post",
 *     "getBySector" = {
 *               "method" = "POST",
 *               "path" = "/companyFilter"
 *     },
 *     "deleteMultiple" = {
 *              "controller"=CompanyController::class,
 *              "method" = "DELETE",
 *              "path" = "/companies"
 *     },},
 *     itemOperations={"get",
 *     }
 * )
 */
class Company
{
    use TimeStampableTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $socialReason;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $address;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=25, nullable=true)
     */
    private $phone;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $companyType;

    /**
     * @ORM\Column(type="string", length=25, nullable=true)
     */
    private $country;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $siret;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $siren;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $tva;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $capital;

    /**
     * @ORM\Column(type="string", length=25, nullable=true)
     */
    private $size;

    /**
     * @ORM\Column(type="string", length=25, nullable=true)
     */
    private $gelee;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $managerEmail;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $managerCivility;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $managerLastname;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $managerFirstname;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $addedBy;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $siteAddress;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Files", cascade={"persist", "remove"})
     */
    private $image;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $postalCode;

    /**
     *
     * @ORM\ManyToMany(targetEntity="LegalStatus", cascade={"persist", "remove"})
     * @ORM\JoinTable(name="company_status",
     *      joinColumns={@ORM\JoinColumn(name="company_id", referencedColumnName="id",onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="status_id", referencedColumnName="id",onDelete="CASCADE")}
     *      )
     */
    private $legalStatus;

    /**
     *
     * @ORM\ManyToMany(targetEntity="Macaron", cascade={"persist", "remove"})
     * @ORM\JoinTable(name="company_macaron",
     *      joinColumns={@ORM\JoinColumn(name="company_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="macaron_id", referencedColumnName="id", onDelete="CASCADE")}
     *      )
     */
    private $macarons;

    /**
     *
     * @ORM\ManyToMany(targetEntity="Tag", cascade={"persist", "remove"},orphanRemoval=true)
     * @ORM\JoinTable(name="company_tag",
     *      joinColumns={@ORM\JoinColumn(name="company_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="tag_id", referencedColumnName="id", onDelete="CASCADE")}
     *      )
     */
    private $tags;

    /**
     *
     * @ORM\ManyToMany(targetEntity="Sector",cascade={"persist", "remove"})
     * @ORM\JoinTable(name="company_sector",
     *      joinColumns={@ORM\JoinColumn(name="company_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="sector_id", referencedColumnName="id", onDelete="CASCADE")}
     *      )
     */
    private $sectors;

    /**
     *
     * @ORM\ManyToMany(targetEntity="Calendar",cascade={"persist", "remove"})
     * @ORM\JoinTable(name="company_calendar",
     *      joinColumns={@ORM\JoinColumn(name="company_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="calendar_id", referencedColumnName="id", onDelete="CASCADE")}
     *      )
     */
    private $calendar;

    /**
     * @ORM\OneToMany(targetEntity="User", mappedBy="company")
     */
    private $users;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity="PublicityFiles", mappedBy="company")
     */
    private $publicityFiles;

    /**
     * @ORM\OneToMany(targetEntity="Thumbnails", mappedBy="company", cascade={"persist", "remove"})
     */
    private $thumbnails;

    /**
     * @ORM\OneToMany(targetEntity=Files::class, mappedBy="company")
     */
    private $gallery;

    public function __construct() {
        $this->calendar = new ArrayCollection();
        $this->legalStatus = new ArrayCollection();
        $this->macarons = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->sectors = new ArrayCollection();
        $this->gallery = new ArrayCollection();
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getSocialReason(): ?string
    {
        return $this->socialReason;
    }

    public function setSocialReason(?string $socialReason): self
    {
        $this->socialReason = $socialReason;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
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


    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getSiret(): ?string
    {
        return $this->siret;
    }

    public function setSiret(?string $siret): self
    {
        $this->siret = $siret;

        return $this;
    }

    public function getSiren(): ?string
    {
        return $this->siren;
    }

    public function setSiren(?string $siren): self
    {
        $this->siren = $siren;

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

    public function getCapital(): ?float
    {
        return $this->capital;
    }

    public function setCapital(?float $capital): self
    {
        $this->capital = $capital;

        return $this;
    }

    public function getSize(): ?string
    {
        return $this->size;
    }

    public function setSize(?string $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function getGelee(): ?string
    {
        return $this->gelee;
    }

    public function setGelee(?string $gelee): self
    {
        $this->gelee = $gelee;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getManagerEmail()
    {
        return $this->managerEmail;
    }

    /**
     * @param mixed $managerEmail
     */
    public function setManagerEmail($managerEmail): void
    {
        $this->managerEmail = $managerEmail;
    }

    /**
     * @return mixed
     */
    public function getManagerCivility()
    {
        return $this->managerCivility;
    }

    /**
     * @param mixed $managerCivility
     */
    public function setManagerCivility($managerCivility): void
    {
        $this->managerCivility = $managerCivility;
    }

    /**
     * @return mixed
     */
    public function getManagerLastname()
    {
        return $this->managerLastname;
    }

    /**
     * @param mixed $managerLastname
     */
    public function setManagerLastname($managerLastname): void
    {
        $this->managerLastname = $managerLastname;
    }

    /**
     * @return mixed
     */
    public function getManagerFirstname()
    {
        return $this->managerFirstname;
    }

    /**
     * @param mixed $managerFirstname
     */
    public function setManagerFirstname($managerFirstname): void
    {
        $this->managerFirstname = $managerFirstname;
    }

    /**
     * @return mixed
     */
    public function getAddedBy()
    {
        return $this->addedBy;
    }

    /**
     * @param mixed $addedBy
     */
    public function setAddedBy($addedBy): void
    {
        $this->addedBy = $addedBy;
    }

    /**
     * @return mixed
     */
    public function getSiteAddress()
    {
        return $this->siteAddress;
    }

    /**
     * @param mixed $siteAddress
     */
    public function setSiteAddress($siteAddress): void
    {
        $this->siteAddress = $siteAddress;
    }

    /**
     * @return mixed
     */
    public function getCompanyType()
    {
        return $this->companyType;
    }

    /**
     * @param mixed $companyType
     */
    public function setCompanyType($companyType): void
    {
        $this->companyType = $companyType;
    }

    /**
     * @return Collection|Calendar[]
     */
    public function getCalendar(): Collection
    {
        return $this->calendar;
    }

    /**
     * @param ArrayCollection $calendar
     */
    public function setCalendar(ArrayCollection $calendar): void
    {
        $this->calendar = $calendar;
    }

    /**
     * @param ArrayCollection $calendar
     */
    public function addCalendar(Calendar $calendar)
    {
        if (!$this->calendar->contains($calendar)) {
            $this->calendar[] = $calendar;
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * @param mixed $users
     */
    public function setUsers($users): void
    {
        $this->users = $users;
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
     * @return ArrayCollection
     */
    public function getLegalStatus(): ArrayCollection
    {
        return $this->legalStatus;
    }

    /**
     * @param ArrayCollection $legalStatus
     */
    public function setLegalStatus(ArrayCollection $legalStatus): void
    {
        $this->legalStatus = $legalStatus;
    }
    /**
     * @param ArrayCollection $legalStatus
     */
    public function addLegalStatus(LegalStatus $legalStatus)
    {
        if (!$this->legalStatus->contains($legalStatus)) {
            $this->legalStatus[] = $legalStatus;
        }
        return $this;
    }

    public function removeLegalStatus(LegalStatus $legalStatus): self
    {
        if ($this->legalStatus->contains($legalStatus)) {
            $this->legalStatus->removeElement($legalStatus);
        }
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getMacarons(): ArrayCollection
    {
        return $this->macarons;
    }

    /**
     * @param ArrayCollection $macarons
     */
    public function addMacarons(Macaron $macaron)
    {
        if (!$this->macarons->contains($macaron)) {
            $this->macarons[] = $macaron;
        }
        return $this;
    }

    public function removeMacarons(Macaron $macaron): self
    {
        if ($this->macarons->contains($macaron)) {
            $this->macarons->removeElement($macaron);
        }
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @param ArrayCollection $tags
     */
    public function addTags(Tag $tag)
    {
        if (!$this->tags->contains($tag)) {
            $this->tags[] = $tag;
        }
        return $this;
    }
    public function removeTags(Tag $tag): self
    {
        if ($this->tags->contains($tag)) {
            $this->tags->removeElement($tag);
        }
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getSectors(): ArrayCollection
    {
        return $this->sectors;
    }

    /**
     * @param ArrayCollection $sectors
     */
    public function addSectors(Sector $sector)
    {
        if (!$this->sectors->contains($sector)) {
            $this->sectors[] = $sector;
        }
        return $this;
    }
    public function removeSectors(Sector $sector): self
    {
        if ($this->sectors->contains($sector)) {
            $this->sectors->removeElement($sector);
        }
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPostalCode()
    {
        return $this->postalCode;
    }

    /**
     * @param mixed $postalCode
     */
    public function setPostalCode($postalCode): void
    {
        $this->postalCode = $postalCode;
    }

    /**
     * @return mixed
     */
    public function getPublicityFiles()
    {
        return $this->publicityFiles;
    }

    /**
     * @param mixed $publicityFiles
     */
    public function setPublicityFiles($publicityFiles): void
    {
        $this->publicityFiles = $publicityFiles;
    }

    /**
     * @return Collection|Files[]
     */
    public function getGallery(): Collection
    {
        return $this->gallery;
    }

    public function addGallery(Files $gallery): self
    {
        if (!$this->gallery->contains($gallery)) {
            $this->gallery[] = $gallery;
            $gallery->setCompany($this);
        }

        return $this;
    }

    public function removeGallery(Files $gallery): self
    {
        if ($this->gallery->removeElement($gallery)) {
            // set the owning side to null (unless already changed)
            if ($gallery->getCompany() === $this) {
                $gallery->setCompany(null);
            }
        }

        return $this;
    }

}
