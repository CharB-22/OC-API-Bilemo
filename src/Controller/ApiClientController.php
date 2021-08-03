<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\EndUser;
use App\Repository\ClientRepository;
use App\Repository\EndUserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializerInterface;
use JMS\Serializer\SerializationContext;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;

use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class ApiClientController extends AbstractController
{
    /**
     * 
     * List the selected client's customers.
     * 
     * @Route("/api/customers", name="api_customers", methods={"GET"})
     * 
     * @OA\Response(
     *     response=200,
     *     description="Returns a list of the customers attached to the selected",
     *     @OA\JsonContent(
     *         type="array",
     *         @OA\Items(ref=@Model(type=EndUser::class))
     *     )
     * )
     * 
     * @OA\Response(response="401",description="Error: Unauthorized.")
     * @OA\Response(response=403, description="Error: Forbidden"),
     * @OA\Response(response=500, description="Error: Internal error")
     * 
     * @OA\Tag(name="Customers")
     * 
     * @Security(name="Bearer")
     * 
     */
    public function getCustomers(
        Request $request,
        EndUserRepository $endUserRepository,
        SerializerInterface $serializer): Response
    {
        
        // Select only the customers attached to the authentified client
        $endUsers = $endUserRepository->findBy(['Client' => $this->getUser()->getId()]);
       
        $json = $serializer->serialize($endUsers, 'json', SerializationContext::create()->setGroups(array('customers:read')));

        $response = new JsonResponse($json, 200, [], true);

        return $response;
    }

    /**
     * 
     * Give all the details about one client's customer.
     * 
     * @Route("/api/customers/{id}", name="api_customers_details", methods={"GET"})
     * 
     * @OA\Response(
     *     response=200,
     *     description="Returns the details of a customer attached to one specific client.",
     *     @OA\JsonContent(
     *         type="array",
     *         @OA\Items(ref=@Model(type=EndUser::class))
     *     )
     * )
     * 
     * @OA\Response(response="401",description="Error: Unauthorized.")
     * @OA\Response(response=403, description="Error: Forbidden"),
     * @OA\Response(response="404",description="Error: Not found.")
     * @OA\Response(response=500, description="Error: Internal error")
     * 
     * @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="The field used to identify one customer.",
     *     @OA\Schema(type="integer"),
     *     @OA\Examples(example="int", value="1",summary="An int value for example")
     * )
     * 
     * @OA\Tag(name="Customers")
     * 
     * @Security(name="Bearer")
     */
    public function getCustomerDetails(
        EndUser $endUser,
        EndUserRepository $endUserRepository,
        SerializerInterface $serializer
    ): Response
    {
        if ($endUser->getClient() !==  $this->getUser())
        {
            throw new AccessDeniedHttpException("Access denied - you don't have the rights to access this customer's details.");
        }

        $json = $serializer->serialize($endUser, 'json', SerializationContext::create()->setGroups(array('customers:read')));

        $response = new JsonResponse($json, 200, [], true);

        return $response;
    }

    /**
     * 
     * Create one customer attached to a selected client.
     * 
     * @Route("/api/customers", name="api_customer_new", methods={"POST"})
     * 
     * @OA\Response(
     *     response=201,
     *     description="Create a new customer attached to the authentified client.",
     *     @OA\JsonContent(
     *         type="array",
     *         @OA\Items(ref=@Model(type=EndUser::class))
     *     )
     * )
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass customer information",
     *    @OA\JsonContent(
     *       required={"name","email"},
     *       @OA\Property(property="name", type="string", example="Harry Potter"),
     *       @OA\Property(property="email", type="string", example="hPotter@hogwarts.com")
     *    ),
     * ),
     * @OA\Response(response="400",description="Error: Bad Request.")
     * @OA\Response(response="401",description="Error: Unauthorized.")
     * @OA\Response(response=403, description="Error: Forbidden"),
     * @OA\Response(response=500, description="Error: Internal error")
     * 
     * @OA\Tag(name="Customers")
     * 
     * @Security(name="Bearer")
     */
    public function createCustomer(
        Request $request, 
        SerializerInterface $serializer, 
        EntityManagerInterface $manager,
        ValidatorInterface $validator
        )
    {

        try {
            
            // Get the information of the new Client from the POST request
            $jsonContent = $request->getContent();
            // Deserialize from json to be saved as an EndUser
            $newCustomer = $serializer->deserialize($jsonContent, EndUser::class, 'json');
            $newCustomer->setClient($this->getUser());

            // Make sure that the info given respect the rules
            $errors = $validator->validate($newCustomer);

            if (count($errors) > 0)
            {
                return $this->json($errors, 400);
            }
            // Push it to the database
            $manager->persist($newCustomer);
            $manager->flush();

            // Return a response to Postman
            return $this->json([
                'status' => 201,
                'message' => 'L\'utilisateur a bien été ajouté'
            ], 201, [], ['groups' => 'customer:read']);

        } catch (NotEncodableValueException $e){

            return $this->json([
                'status' => 400,
                'message' => $e->getMessage()
            ], 400);
        }

    }

    /**
     * Delete one selected client's customer.
     * 
     * @Route("/api/customers/{id}", name="api_customer_delete", methods={"DELETE"})
     * 
     * @OA\Response(
     *     response=204,
     *     description="Delete a customer attached to a selected client.",
     *     @OA\JsonContent(
     *         type="array",
     *         @OA\Items(ref=@Model(type=EndUser::class))
     *     )
     * )
     * 
     * @OA\Response(response="401",description="Error: Unauthorized.")
     * @OA\Response(response=403, description="Error: Forbidden")
	 * @OA\Response(response="404",description="Error: Not found.")
     * @OA\Response(response=500, description="Error: Internal error")
     * 
     * @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="The field used to identify one customer.",
     *     @OA\Schema(type="string"),
     *     @OA\Examples(example="int", value="100",summary="An int value for example")
     * )
     * 
     * @OA\Tag(name="Customers")
     * 
     * @Security(name="Bearer")
     */
    public function deleteCustomer(EndUser $endUser, EntityManagerInterface $manager)
    {

        if ($endUser->getClient() !==  $this->getUser())
        {
            throw new AccessDeniedHttpException("Access denied - you don't have the rights to delete this customer.");
        }

        // Remove the element from the database
        $manager->remove($endUser);
        $manager->flush();

        return $this->json( null, 204);
        
    }
}
