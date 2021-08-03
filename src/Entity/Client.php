<?php

namespace App\Entity;

use App\Repository\ClientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

use JMS\Serializer\Annotation\Groups;
use Hateoas\Configuration\Annotation as Hateoas;    
use OpenApi\Annotations as OA;

/**
 * @ORM\Entity(repositoryClass=ClientRepository::class)
 */
class Client implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @OA\Property(type="int", description="The unique identifier of the client.")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @OA\Property(type="string", description="The email of the client.")
     * @Groups({"customers:read"})
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @OA\Property(type="string", description="The hash password of the client in order to access the API.")
     */
    private $password;


    /**
     * @ORM\OneToMany(targetEntity=EndUser::class, mappedBy="Client", orphanRemoval=true)
     * @OA\Property(description="The customers related to this client."))
     */
    private $endUsers;

    /**
     * @ORM\Column(type="string", length=255)
     * @OA\Property(type="string", description="The client's username in order to access the API.")
     * @Groups({"customers:read"})
     */
    private $username;

    public function __construct()
    {
        $this->endUsers = new ArrayCollection();
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
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->username;
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

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }


    /**
     * @return Collection|EndUser[]
     */
    public function getEndUsers(): Collection
    {
        return $this->endUsers;
    }

    public function addEndUser(EndUser $endUser): self
    {
        if (!$this->endUsers->contains($endUser)) {
            $this->endUsers[] = $endUser;
            $endUser->setClient($this);
        }

        return $this;
    }

    public function removeEndUser(EndUser $endUser): self
    {
        if ($this->endUsers->removeElement($endUser)) {
            // set the owning side to null (unless already changed)
            if ($endUser->getClient() === $this) {
                $endUser->setClient(null);
            }
        }

        return $this;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }
}
