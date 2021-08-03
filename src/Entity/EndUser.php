<?php

namespace App\Entity;

use App\Repository\EndUserRepository;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;

use JMS\Serializer\Annotation\Groups;
use Hateoas\Configuration\Annotation as Hateoas;
use OpenApi\Annotations as OA;

/**
 * @ORM\Entity(repositoryClass=EndUserRepository::class)
 * @Hateoas\Relation(
 *     "self", 
 *     href = @Hateoas\Route(
 *         "api_customers",
 *         parameters = { "id" = "expr(object.getId())" },
 *   				absolute= true
 *      ),
 *     exclusion = @Hateoas\Exclusion(groups={"customers:read"})
 * ),
 * @Hateoas\Relation(
 *     "delete", 
 *     href = @Hateoas\Route(
 *         "api_customer_delete",
 *         parameters = { "id" = "expr(object.getId())" },
 *   				absolute= true
 *      ),
 *     exclusion = @Hateoas\Exclusion(groups={"customers:read"})
 * ),
 * @Hateoas\Relation(
 *     "create", 
 *     href = @Hateoas\Route(
 *         "api_customer_new"
 *      ),
 *     exclusion = @Hateoas\Exclusion(groups={"customers:read"})
 * )
 * 
 */
class EndUser
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"customers:read"})
     * @OA\Property(type="int", description="The unique identifier of the enduser.")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"customers:read"})
     * @Assert\NotBlank(
     *     message = "Cette information doit être renseignée pour la création de cet utilisateur."
     * )
     * @OA\Property(type="string", description="End user's first name.")
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"customers:read"})
     * @Assert\NotBlank(
     *     message = "Cette information doit être renseignée pour la création de cet utilisateur."
     * )
     * @OA\Property(type="string", description="End user's email.")
     */
    private $email;

    /**
     * @ORM\ManyToOne(targetEntity=Client::class, inversedBy="endUsers")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"customers:read"})
     * @OA\Property(type="object", description="The client whom the end user is attached to.")
     */
    private $Client;

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
