<?php

namespace App\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="publicity_files")
 */
class PublicityFiles
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Files", inversedBy="publicityFiles")
     * @ORM\JoinColumn(name="image_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $image;

    /**
     * @ORM\ManyToOne(targetEntity="Files", inversedBy="publicityFiles")
     * @ORM\JoinColumn(name="pdf_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $pdf;

    /**
     * @ORM\ManyToOne(targetEntity="Profile", inversedBy="publicityFiles")
     * @ORM\JoinColumn(name="profile_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $profile;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $links;
    /**
     * @ORM\ManyToOne(targetEntity="PublicityCompany", inversedBy="publicityFiles")
     * @ORM\JoinColumn(name="publicity_company_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $publicityCompany;
    /**
     * @ORM\ManyToOne(targetEntity="Company", inversedBy="publicityFiles")
     * @ORM\JoinColumn(name="company_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $company;

    public function __construct() {
        //$this->image = new ArrayCollection();
        //$this->pdf = new ArrayCollection();
    }

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
    public function getProfile()
    {
        return $this->profile;
    }

    /**
     * @param mixed $profile
     */
    public function setProfile($profile): void
    {
        $this->profile = $profile;
    }

    /**
     * @return mixed
     */
    public function getLinks()
    {
        return $this->links;
    }

    /**
     * @param mixed $links
     */
    public function setLinks($links): void
    {
        $this->links = $links;
    }

    /**
     * @return mixed
     */
    public function getPublicityCompany()
    {
        return $this->publicityCompany;
    }

    /**
     * @param mixed $publicityCompany
     */
    public function setPublicityCompany($publicityCompany): void
    {
        $this->publicityCompany = $publicityCompany;
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
}