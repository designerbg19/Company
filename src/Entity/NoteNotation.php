<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="note_notation")
 */
class NoteNotation
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $value;

    /**
     * @ORM\ManyToOne(targetEntity="Note", inversedBy="noteNotation")
     * @ORM\JoinColumn(name="note_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $note;

    /**
     * @ORM\ManyToOne(targetEntity="Notation", inversedBy="noteNotation")
     * @ORM\JoinColumn(name="notation_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $notation;

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
    public function getNotation()
    {
        return $this->notation;
    }

    /**
     * @param mixed $notation
     */
    public function setNotation($notation): void
    {
        $this->notation = $notation;
    }

}