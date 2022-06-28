<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="note_questionnaire")
 */
class NoteQuestionnaire
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
    private $value;

    /**
     * @ORM\ManyToOne(targetEntity="Note", inversedBy="noteQuestionnaire")
     * @ORM\JoinColumn(name="note_id", referencedColumnName="id",onDelete="CASCADE")
     */
    private $note;

    /**
     * @ORM\ManyToOne(targetEntity="Questionnaire", inversedBy="noteQuestionnaire")
     * @ORM\JoinColumn(name="questionnaire_id", referencedColumnName="id",onDelete="CASCADE")
     */
    private $questionnaire;

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
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value): void
    {
        $this->value = $value;
    }

    /**
     * @return mixed
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * @param mixed $note
     */
    public function setNote($note): void
    {
        $this->note = $note;
    }

    /**
     * @return mixed
     */
    public function getQuestionnaire()
    {
        return $this->questionnaire;
    }

    /**
     * @param mixed $questionnaire
     */
    public function setQuestionnaire($questionnaire): void
    {
        $this->questionnaire = $questionnaire;
    }

}