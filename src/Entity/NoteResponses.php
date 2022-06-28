<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Traits\TimeStampableTrait;
use App\Controller\NoteResponseController;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\NoteResponsesRepository")
 * @ORM\Table(name="note_responses")
 * @ORM\HasLifecycleCallbacks()
 * @ApiResource(
 *     collectionOperations={
 *     "get" = {
 *              "method" = "GET",
 *              "path" = "/noteResponses"
 *          },
 *      "deletemultiple" = {
 *              "controller"=NoteResponseController::class,
 *              "method" = "DELETE",
 *              "path" = "/noteResponses"
 *          },
 *     },
 *     itemOperations={
 *     "createNoteResponse" = {
 *              "controller"=NoteResponseController::class,
 *               "method" = "POST",
 *               "path" = "/noteResponses/{idNote}"
 *     },
 *     "editNoteResponse" = {
 *              "controller"=NoteResponseController::class,
 *               "method" = "POST",
 *               "path" = "/noteResponse/{id}"
 *     },
 *     "get" = {
 *              "method" = "GET",
 *              "path" = "/noteResponses/{id}"
 *          },
 *     }
 * )
 */
class NoteResponses
{
    use TimeStampableTrait;
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
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $published;

    /**
     * @ORM\ManyToOne(targetEntity="Note", inversedBy="noteResponses")
     * @ORM\JoinColumn(name="note_id", referencedColumnName="id",onDelete="CASCADE")
     */
    private $note;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="noteResponses")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id",onDelete="CASCADE")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="noteResponses")
     * @ORM\JoinColumn(name="respond_to", referencedColumnName="id",onDelete="CASCADE")
     */
    private $respondTo;

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
    public function getPublished()
    {
        return $this->published;
    }

    /**
     * @param mixed $published
     */
    public function setPublished($published): void
    {
        $this->published = $published;
    }

    /**
     * @return mixed
     */
    public function getRespondTo()
    {
        return $this->respondTo;
    }

    /**
     * @param mixed $respondTo
     */
    public function setRespondTo($respondTo): void
    {
        $this->respondTo = $respondTo;
    }

}