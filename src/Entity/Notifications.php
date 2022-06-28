<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\NotificationsRepository;
use App\Controller\NotificationsController;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=NotificationsRepository::class)
 * @ApiResource(
 *      collectionOperations={
 *     "get" = {
 *              "method" = "GET",
 *              "path" = "/notifications"
 *          },
 *      "delete" = {
 *              "controller"=NotificationsController::class,
 *              "method" = "DELETE",
 *              "path" = "/notifications"
 *     }
 *     },
 *     itemOperations={
 *     "get" = {
 *               "method" = "GET",
 *               "path" = "/notifications/{id}"
 *     },
 *     "getVisualized" = {
 *              "controller"=NotificationsController::class,
 *               "method" = "POST",
 *               "path" = "/notificationsVisualized/{id}"
 *     },
 *     "getByUser" = {
 *              "controller"=NotificationsController::class,
 *               "method" = "GET",
 *               "path" = "/notificationsByUser/{idUser}"
 *     },
 *     }
 * )
 */
class Notifications
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $Date;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $message;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $visualized;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $link;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->Date;
    }

    public function setDate(\DateTimeInterface $Date): self
    {
        $this->Date = $Date;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): self
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getVisualized()
    {
        return $this->visualized;
    }

    /**
     * @param mixed $visualized
     */
    public function setVisualized($visualized): void
    {
        $this->visualized = $visualized;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(?string $link): self
    {
        $this->link = $link;

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

}
