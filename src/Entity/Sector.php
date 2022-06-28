<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\SectorRepository;
use App\Controller\SectorController;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SectorRepository::class)
 *  @ApiResource(
 *     collectionOperations={"get","post",
 *      "deleteMultipe" = {
 *              "controller"=SectorController::class,
 *              "method" = "DELETE",
 *              "path" = "/sectors"
 *     }
 *     },
 *     itemOperations={"get", "put"}
 * )
 */
class Sector
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
     *
     * @ORM\ManyToMany(targetEntity="Naf",cascade={"persist", "remove"})
     * @ORM\JoinTable(name="naf_sector",
     *      joinColumns={@ORM\JoinColumn(name="sector_id", referencedColumnName="id",onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="naf_id", referencedColumnName="id", onDelete="CASCADE")}
     *      )
     */
    private $nafs;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Sector",cascade={"persist"})
     */
    private $parent;

    /**
     * @ORM\ManyToOne(targetEntity=Files::class)
     */
    private $image;

    public function __construct()
    {
        $this->nafs = new ArrayCollection();
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

    /**
     * @return ArrayCollection
     */
    public function getNafs(): ArrayCollection
    {
        return $this->nafs;
    }

    /**
     * @param ArrayCollection $nafs
     */
    public function addNafs(Naf $naf)
    {
        if (!$this->nafs->contains($naf)) {
            $this->nafs[] = $naf;
        }
        return $this;
    }

    public function removeNafs(Naf $naf): self
    {
        if ($this->nafs->contains($naf)) {
            $this->nafs->removeElement($naf);
        }
        return $this;
    }

    /**
     * @return mixed
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param mixed $parent
     */
    public function setParent($parent): void
    {
        $this->parent = $parent;
    }

    public function getImage(): ?Files
    {
        return $this->image;
    }

    public function setImage(?Files $image): self
    {
        $this->image = $image;

        return $this;
    }


}
