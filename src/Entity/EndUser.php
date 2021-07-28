<?php

namespace App\Entity;

use App\Repository\EndUserRepository;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as Serializer;
use Hateoas\Configuration\Annotation as Hateoas;


/**
 * @ORM\Entity(repositoryClass=EndUserRepository::class)
 * @Hateoas\Relation("self", href = "expr('/api/clients/{company}/customers/' ~ object.getLastName())")
 * @Hateoas\Relation("delete", href = "expr('/api/clients/{company}/customers/' ~ object.getId())")
 */
class EndUser
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"customers:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"customers:read"})
     * @Assert\NotBlank(
     *     message = "Cette information doit être renseignée pour la création de cet utilisateur."
     * )
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"customers:read"})
     * @Assert\NotBlank(
     *     message = "Cette information doit être renseignée pour la création de cet utilisateur."
     * )
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"customers:read"})
     * @Assert\NotBlank(
     *     message = "Cette information doit être renseignée pour la création de cet utilisateur."
     * )
     * @Assert\Email(
     *     message = "L'email '{{ value }}' ne respecte pas le format."
     * )
     */
    private $email;

    /**
     * @ORM\ManyToOne(targetEntity=Client::class, inversedBy="endUsers")
     * @ORM\JoinColumn(nullable=false)
     */
    private $Client;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
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

    public function getClient(): ?Client
    {
        return $this->Client;
    }

    public function setClient(?Client $Client): self
    {
        $this->Client = $Client;

        return $this;
    }
}
