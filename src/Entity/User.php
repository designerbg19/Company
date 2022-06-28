<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use App\Controller\UserController;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\SerializedName;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 *
 * @ApiResource(
 *     collectionOperations={"get",
 *     "register"={
 *             "route_name"="user_registration",
 *              "method"="POST",
 *         },
 *     "app_forgotten_password"={
 *             "route_name"="app_forgotten_password",
 *              "method"="POST",
 *       },
 *      "app_edit_password"={
 *             "route_name"="app_reset_password",
 *             "method"="POST",
 *       },
 *      "app_login"={
 *             "route_name"="user_login",
 *             "method"="POST",
 *       },
 *      "refeach_token"={
 *             "route_name"="gesdinet_jwt_refresh_token",
 *              "method"="POST",
 *         },
 *      "send_email_admin"={
 *              "method" = "POST",
 *             "path" = "/sendEmailAdmin/{email}"
 *         },
 *      "edit_passwrd_user"={
 *              "method" = "POST",
 *              "path" = "/updatePassword/{id}"
 *         },
 *      "account_enabled"={
 *              "method" = "POST",
 *              "path" = "/professionalAccountEnabled/{id}"
 *         },
 *      "remove_account_enabled"={
 *              "method" = "POST",
 *              "path" = "/removeProfessionalAccount/{id}"
 *         },
 *     "deleteMultiple" = {
 *              "controller"=UserController::class,
 *              "method" = "DELETE",
 *              "path" = "/users"
 *     }
 *     },
 *     itemOperations={"get", "put",
 *     }
 * )
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     *
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $username;

    /**
     * @Groups("user:write")
     *
     * @SerializedName("password")
     */
    private $plainPassword;
    /**
     * @ORM\Column(type="string", length=25, nullable=true)
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=25, nullable=true)
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=25)
     */
    private $civility;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $site;

    /**
     * @ORM\Column(type="string", length=25, nullable=true)
     */
    private $country;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $birthDate;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $enabled;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $professionalProfile=false;

    /**
     * @ORM\Column(type="string", length=25, nullable=true)
     */
    private $tva;

    /**
     * @ORM\Column(type="string", length=25, nullable=true)
     */
    private $siren;

    /**
     * @ORM\Column(type="string", length=25, nullable=true)
     */
    private $emailPro;

    /**
     * @ORM\Column(type="string", length=25, nullable=true)
     */
    private $status;

    /**
     * @ORM\Column(type="string", length=25, nullable=true)
     */
    private $nationality;

    /**
     * @ORM\Column(type="string", length=25, nullable=true)
     */
    private $responsable;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastLogin;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $address;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $webSite;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $codePostal;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $mediaManager;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $reset_token;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Files", cascade={"persist", "remove"})
     */
    private $images;

    /**
     * @ORM\ManyToOne(targetEntity="Company", inversedBy="users")
     * @ORM\JoinColumn(name="company_id", referencedColumnName="id")
     */
    private $company;

    /**
     * @ORM\OneToMany(targetEntity="NoteResponses", mappedBy="note")
     */
    private $noteResponses;

    /**
     * @ORM\ManyToOne(targetEntity="Badge", inversedBy="users")
     * @ORM\JoinColumn(name="badge_id", referencedColumnName="id")
     */
    private $badge;

    /**
     *
     * @ORM\ManyToMany(targetEntity="Leisure", cascade={"persist", "remove"})
     * @ORM\JoinTable(name="user_leisure",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id",onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="leisure_id", referencedColumnName="id",onDelete="CASCADE")}
     *      )
     */
    private $leisure;

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->roles = array('ROLE_USER');
        $this->enabled = false;
        $this->leisure = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function hasRoles(string $roles): bool
    {
        return in_array($roles, $this->roles);
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        $this->plainPassword = null;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * @param mixed $firstname
     */
    public function setFirstname($firstname): void
    {
        $this->firstname = $firstname;
    }

    /**
     * @return mixed
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * @param mixed $lastname
     */
    public function setLastname($lastname): void
    {
        $this->lastname = $lastname;
    }

    /**
     * @return mixed
     */
    public function getCivility()
    {
        return $this->civility;
    }

    /**
     * @param mixed $civility
     */
    public function setCivility($civility): void
    {
        $this->civility = $civility;
    }

    /**
     * @return mixed
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * @param mixed $site
     */
    public function setSite($site): void
    {
        $this->site = $site;
    }

    /**
     * @return mixed
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param mixed $country
     */
    public function setCountry($country): void
    {
        $this->country = $country;
    }

    /**
     * @return mixed
     */
    public function getBirthDate()
    {
        return $this->birthDate;
    }

    /**
     * @param mixed $birthDate
     */
    public function setBirthDate($birthDate): void
    {
        $this->birthDate = $birthDate;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @param bool $enabled
     */
    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }

    /**
     * @return mixed
     */
    public function getTva()
    {
        return $this->tva;
    }

    /**
     * @param mixed $tva
     */
    public function setTva($tva): void
    {
        $this->tva = $tva;
    }

    /**
     * @return mixed
     */
    public function getSiren()
    {
        return $this->siren;
    }

    /**
     * @param mixed $siren
     */
    public function setSiren($siren): void
    {
        $this->siren = $siren;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status): void
    {
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function getNationality()
    {
        return $this->nationality;
    }

    /**
     * @param mixed $nationality
     */
    public function setNationality($nationality): void
    {
        $this->nationality = $nationality;
    }

    /**
     * @return mixed
     */
    public function getResponsable()
    {
        return $this->responsable;
    }

    /**
     * @param mixed $responsable
     */
    public function setResponsable($responsable): void
    {
        $this->responsable = $responsable;
    }

    /**
     * @return mixed
     */
    public function getLastLogin()
    {
        return $this->lastLogin;
    }

    /**
     * @param mixed $lastLogin
     */
    public function setLastLogin($lastLogin): void
    {
        $this->lastLogin = $lastLogin;
    }


    public function getResetToken()
    {
        return $this->reset_token;
    }

    /**
     * @param mixed $reset_token
     */
    public function setResetToken($reset_token): void
    {
        $this->reset_token = $reset_token;
    }

    /**
     * @return mixed
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * @param mixed $images
     */
    public function setImages($images): void
    {
        $this->images = $images;
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
    public function getNoteResponses()
    {
        return $this->noteResponses;
    }

    /**
     * @param mixed $noteResponses
     */
    public function setNoteResponses($noteResponses): void
    {
        $this->noteResponses = $noteResponses;
    }

    /**
     * @return ArrayCollection
     */
    public function getLeisure(): ArrayCollection
    {
        return $this->leisure;
    }

    /**
     * @param Leisure $leisure
     */
    public function addLeisure(Leisure $leisure)
    {
        if (!$this->leisure->contains($leisure)) {
            $this->leisure[] = $leisure;
        }
        return $this;
    }

    public function removeLeisure(Leisure $leisure): self
    {
        if ($this->leisure->contains($leisure)) {
            $this->leisure->removeElement($leisure);
        }
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param mixed $address
     */
    public function setAddress($address): void
    {
        $this->address = $address;
    }

    /**
     * @return mixed
     */
    public function getWebSite()
    {
        return $this->webSite;
    }

    /**
     * @param mixed $webSite
     */
    public function setWebSite($webSite): void
    {
        $this->webSite = $webSite;
    }

    /**
     * @return mixed
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param mixed $phone
     */
    public function setPhone($phone): void
    {
        $this->phone = $phone;
    }

    /**
     * @return mixed
     */
    public function getCodePostal()
    {
        return $this->codePostal;
    }

    /**
     * @param mixed $codePostal
     */
    public function setCodePostal($codePostal): void
    {
        $this->codePostal = $codePostal;
    }

    /**
     * @return mixed
     */
    public function getMediaManager()
    {
        return $this->mediaManager;
    }

    /**
     * @param mixed $mediaManager
     */
    public function setMediaManager($mediaManager): void
    {
        $this->mediaManager = $mediaManager;
    }

    /**
     * @return bool
     */
    public function isProfessionalProfile(): bool
    {
        return $this->professionalProfile;
    }

    /**
     * @param bool $professionalProfile
     */
    public function setProfessionalProfile(bool $professionalProfile): void
    {
        $this->professionalProfile = $professionalProfile;
    }

    /**
     * @return mixed
     */
    public function getEmailPro()
    {
        return $this->emailPro;
    }

    /**
     * @param mixed $emailPro
     */
    public function setEmailPro($emailPro): void
    {
        $this->emailPro = $emailPro;
    }

    /**
     * @return mixed
     */
    public function getBadge()
    {
        return $this->badge;
    }

    /**
     * @param mixed $badge
     */
    public function setBadge($badge): void
    {
        $this->badge = $badge;
    }

}