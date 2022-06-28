<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Traits\TimeStampableTrait;
use App\Repository\NoteRepository;
use App\Controller\NoteController;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=NoteRepository::class)
 * @ORM\HasLifecycleCallbacks()
 *  @ApiResource(
 *     collectionOperations={"get"= {
 *              "method" = "GET",
 *              "path" = "/notes"
 *     }
 * ,"post",
 *      "deleteMultiple" = {
 *              "controller"=NoteController::class,
 *              "method" = "DELETE",
 *              "path" = "/notes"
 *     },
 *     },
 *     itemOperations={"get"= {
 *              "method" = "GET",
 *              "path" = "/notes/{id}"
 *     }
 *     }
 * )
 */
class Note
{
    use TimeStampableTrait;
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="boolean")
     */
    private $valide;

    /**
     * @ORM\Column(type="text")
     */
    private $content;

    /**
     * @ORM\Column(type="boolean")
     */
    private $activeProfile;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $underProfile;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $startDate;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $endDate;

    /**
     * @ORM\OneToMany(targetEntity="NoteNotation", mappedBy="note",cascade={"persist", "remove"})
     */
    private $noteNotation;

    /**
     * @ORM\OneToMany(targetEntity="NoteQuestionnaire", mappedBy="note",cascade={"persist", "remove"})
     */
    private $noteQuestionnaire;

    /**
     * @ORM\OneToMany(targetEntity="NoteResponses", mappedBy="note",cascade={"persist", "remove"})
     */
    private $noteResponses;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="Profile")
     * @ORM\JoinColumn(name="profile_id", referencedColumnName="id")
     */
    private $profile;

    /**
     * @ORM\ManyToOne(targetEntity="Company")
     * @ORM\JoinColumn(name="company_id", referencedColumnName="id")
     */
    private $company;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $score;


    public function __construct()
    {
        $this->noteNotation = new ArrayCollection();
        $this->noteQuestionnaire = new ArrayCollection();
        $this->noteResponses = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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
    public function getValide()
    {
        return $this->valide;
    }

    /**
     * @param mixed $valide
     */
    public function setValide($valide): void
    {
        $this->valide = $valide;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param mixed $content
     */
    public function setContent($content): void
    {
        $this->content = $content;
    }

    /**
     * @return mixed
     */
    public function getActiveProfile()
    {
        return $this->activeProfile;
    }

    /**
     * @param mixed $activeProfile
     */
    public function setActiveProfile($activeProfile): void
    {
        $this->activeProfile = $activeProfile;
    }

    /**
     * @return mixed
     */
    public function getUnderProfile()
    {
        return $this->underProfile;
    }

    /**
     * @param mixed $underProfile
     */
    public function setUnderProfile($underProfile): void
    {
        $this->underProfile = $underProfile;
    }

    /**
     * @return mixed
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @param mixed $startDate
     */
    public function setStartDate($startDate): void
    {
        $this->startDate = $startDate;
    }

    /**
     * @return mixed
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @param mixed $endDate
     */
    public function setEndDate($endDate): void
    {
        $this->endDate = $endDate;
    }

    /**
     * @return mixed
     */
    public function getNoteNotation()
    {
        return $this->noteNotation;
    }

    /**
     * @param mixed $noteNotation
     */
    public function setNoteNotation($noteNotation): void
    {
        $this->noteNotation = $noteNotation;
    }
    /**
     * @param ArrayCollection $noteNotation
     */
    public function addNoteNotation(NoteNotation $notation)
    {
        if (!$this->noteNotation->contains($notation)) {
            $this->noteNotation[] = $notation;
        }
        return $this;
    }

    public function removeNoteNotation(NoteNotation $notation): self
    {
        if ($this->noteNotation->contains($notation)) {
            $this->noteNotation->removeElement($notation);
        }
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNoteQuestionnaire()
    {
        return $this->noteQuestionnaire;
    }

    /**
     * @param ArrayCollection $noteQuestionnaire
     */
    public function addNoteQuestionnaire(NoteQuestionnaire $noteQuestionnaire)
    {
        if (!$this->noteQuestionnaire->contains($noteQuestionnaire)) {
            $this->noteQuestionnaire[] = $noteQuestionnaire;
        }
        return $this;
    }
    public function removeNoteQuestionnaire(NoteQuestionnaire $noteQuestionnaire)
    {
        if ($this->noteQuestionnaire->contains($noteQuestionnaire)) {
            $this->noteQuestionnaire->removeElement($noteQuestionnaire);
        }
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getNoteResponses(): ArrayCollection
    {
        return $this->noteResponses;
    }

    /**
     * @param ArrayCollection $noteResponses
     */
    public function addNoteResponses(NoteResponses $noteResponses)
    {
        if (!$this->noteResponses->contains($noteResponses)) {
            $this->noteResponses[] = $noteResponses;
        }
        return $this;
    }
    public function removeNoteResponses(NoteResponses $noteResponses)
    {
        if ($this->noteResponses->contains($noteResponses)) {
            $this->noteResponses->removeElement($noteResponses);
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
    public function getScore()
    {
        return $this->score;
    }

    /**
     * @param mixed $score
     */
    public function setScore($score): void
    {
        $this->score = $score;
    }

    public function getNoteNotationPrice()
    {
        $result = array();
        foreach ($this->getNoteNotation() as $noteNotation) {
            if ($noteNotation->getNotation()->getCriterion() == "Critère prix") {
                array_push($result, $noteNotation);
            }
        }
        return $result;
    }

    public function getNoteMoyennePrix()
    {
        $length = 0;
        $sum = 0;
        foreach ($this->getNoteNotationPrice() as $noteNotation) {
            if ($noteNotation->getValue() > 0) {
                $length++;
                $sum += $noteNotation->getValue();
            }
        }
        if ($length == 0)
            return 0;
        return round($sum / $length, 2);
    }

    public function getNoteNotationSociete()
    {
        $result = array();
        foreach ($this->getNoteNotation() as $noteNotation) {
            if ($noteNotation->getNotation()->getCriterion() == "Critère société") {
                array_push($result, $noteNotation);
            }
        }
        return $result;
    }

    public function getNoteMoyenneSociete()
    {
        $length = 0;
        $sum = 0;
        foreach ($this->getNoteNotationSociete() as $noteNotation) {
            if ($noteNotation->getValue() > 0) {
                $length++;
                $sum += $noteNotation->getValue();
            }
        }
        if ($length == 0)
            return 0;
        return round($sum / $length, 1);
    }

    public function getNoteNotationsEnvironement()
    {
        $result = array();
        foreach ($this->getNoteNotation() as $noteNotation) {
            if ($noteNotation->getNotation()->getCriterion() == "Critère environement") {
                array_push($result, $noteNotation);
            }
        }
        return $result;
    }

    public function getNoteMoyenneEnvironement()
    {
        $length = 0;
        $sum = 0;
        foreach ($this->getNoteNotationsEnvironement() as $noteNotation) {
            if ($noteNotation->getValue() > 0) {
                $length++;
                $sum += $noteNotation->getValue();
            }
        }
        if ($length == 0)
            return 0;
        return round($sum / $length, 2);
    }

    public function getNoteNotationsCritere()
    {
        $result = array();
        foreach ($this->getNoteNotation() as $noteNotation) {
            if ($noteNotation->getNotation()->getCriterion() == "Critère de notation") {
                array_push($result, $noteNotation);
            }
        }
        return $result;
    }

    public function getNoteMoyenneCritere()
    {
        $length = 0;
        $sum = 0;
        foreach ($this->getNoteNotationsCritere() as $noteNotation) {
            if ($noteNotation->getValue() > 0) {
                $length++;
                $sum += $noteNotation->getValue();
            }
        }
        if ($length == 0)
            return 0;
        return round($sum / $length, 2);
    }
}
