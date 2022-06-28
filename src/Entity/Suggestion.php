<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\SuggestionRepository;
use App\Controller\SuggestionController;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource(
 *      collectionOperations={
 *     "get" = {
 *              "method" = "GET",
 *              "path" = "/suggestions"
 *          },
 *     "post" = {
 *              "method" = "POST",
 *              "path" = "/suggestions.{_format}"
 *     },
 *      "deleteMultipe" = {
 *              "controller"=SuggestionController::class,
 *              "method" = "DELETE",
 *              "path" = "/suggestions"
 *     },
 *      "reportProblem" = {
 *              "controller"=SuggestionController::class,
 *              "method" = "POST",
 *              "path" = "/reportProblem"
 *     }
 *     },
 *     itemOperations={
 *     "get" = {
 *               "method" = "GET",
 *               "path" = "/suggestions/{id}"
 *     },
 *     "editStatus" = {
 *              "controller"=SuggestionController::class,
 *               "method" = "POST",
 *               "path" = "/vuStatus/{id}"
 *     },
 *     "suggestionLogo" = {
 *              "controller"=SuggestionController::class,
 *               "method" = "POST",
 *               "path" = "/suggestionsLogo/{idCompany}"
 *     },
 *     }
 * )
 * @ORM\Entity(repositoryClass=SuggestionRepository::class)
 */
class Suggestion
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
    private $message;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $vuStatus;

    /**
     * @ORM\ManyToOne(targetEntity="Company")
     * @ORM\JoinColumn(name="company_id", referencedColumnName="id",onDelete="CASCADE")
     */
    private $company;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     *
     * @ORM\ManyToMany(targetEntity="Tag", cascade={"persist", "remove"},orphanRemoval=true)
     * @ORM\JoinTable(name="suggestion_tag",
     *      joinColumns={@ORM\JoinColumn(name="suggestion_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="tag_id", referencedColumnName="id", onDelete="CASCADE")}
     *      )
     */
    private $tags;

    /**
     *
     * @ORM\ManyToMany(targetEntity="Sector",cascade={"persist", "remove"})
     * @ORM\JoinTable(name="suggestion_sector",
     *      joinColumns={@ORM\JoinColumn(name="suggestion_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="sector_id", referencedColumnName="id", onDelete="CASCADE")}
     *      )
     */
    private $sectors;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Files", cascade={"persist", "remove"})
     */
    private $logo;

    public function __construct() {
        $this->tags = new ArrayCollection();
        $this->sectors = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param mixed $message
     */
    public function setMessage($message): void
    {
        $this->message = $message;
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
    public function getVuStatus()
    {
        return $this->vuStatus;
    }

    /**
     * @param mixed $vuStatus
     */
    public function setVuStatus($vuStatus): void
    {
        $this->vuStatus = $vuStatus;
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
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return ArrayCollection
     */
    public function getTags(): ArrayCollection
    {
        return $this->tags;
    }

    /**
     * @return ArrayCollection
     */
    public function getSectors(): ArrayCollection
    {
        return $this->sectors;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user): void
    {
        $this->user = $user;
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
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * @param mixed $logo
     */
    public function setLogo($logo): void
    {
        $this->logo = $logo;
    }

}
