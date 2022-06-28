<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\QuestionnaireRepository;
use App\Controller\QuestionnaireController;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=QuestionnaireRepository::class)
 *  @ApiResource(
 *     collectionOperations={"get","post",
 *      "deleteMultipe" = {
 *              "controller"=QuestionnaireController::class,
 *              "method" = "DELETE",
 *              "path" = "/questionnaires"
 *     }
 *     },
 *     itemOperations={"get", "put"}
 * )
 */
class Questionnaire
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
     * @ORM\Column(type="text")
     */
    private $content;

    /**
     * @ORM\ManyToMany(targetEntity="Profile")
     * @ORM\JoinTable(name="profile_questionnaire",
     *      joinColumns={@ORM\JoinColumn(name="questionnaire_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="profile_id", referencedColumnName="id")}
     *      )
     */
    private $profiles;

    /**
     * @ORM\OneToMany(targetEntity="NoteQuestionnaire", mappedBy="note")
     */
    private $noteQuestionnaire;


    public function __construct()
    {
        $this->profiles = new ArrayCollection();
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

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getProfiles(): ArrayCollection
    {
        return $this->profiles;
    }

    /**
     * @param ArrayCollection $profile
     */
    public function addProfiles(Profile $profile)
    {
        if (!$this->profiles->contains($profile)) {
            $this->profiles[] = $profile;
        }
        return $this;
    }

    public function removeProfiles(Profile $profile): self
    {
        if ($this->profiles->contains($profile)) {
            $this->profiles->removeElement($profile);
        }
        return $this;
    }
}
