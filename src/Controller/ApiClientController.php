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
     * List all of Bilemo clients.
     * 
     * @Route("/api/clients", name="api_clients_list", methods={"GET"})
     * 
     * @OA\Response(
     *     response=200,
     *     description="Returns the list of the clients.",
     *     @OA\JsonContent(
     *         type="array",
     *         @OA\Items(ref=@Model(type=Client::class))
     *     )
     * )
     * 
     * @OA\Tag(name="Client")
     * 
     * @Security(name="Bearer")
     * 
     */
    public function getClientList(ClientRepository $clientRepository, SerializerInterface $serializer): Response
    {
        
        $clients = $clientRepository->findAll();
        
        $json = $serializer->serialize($clients, 'json', SerializationContext::create()->setGroups(array('clients:read')));

        $response = new JsonResponse($json, 200, [], true);
       
        return $response;
    }
    
    /**
     * Give details on one selected client.
     * 
     * @Route("/api/clients/{id}", name="api_client_details", methods={"GET"})
     * 
     * @OA\Response(
     *     response=200,
     *     description="Returns the details of a selected client.",
     *     @OA\JsonContent(
     *         type="array",
     *         @OA\Items(ref=@Model(type=Client::class))
     *     )
     * )
     * 
     * @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="The field used to select one client.",
     *     @OA\Schema(type="string")
     * )
     * 
     * @OA\Tag(name="Client")
     * 
     * @Security(name="Bearer")
     * 
     */
    public function getClient(ClientRepository $clientRepository, Client $client, SerializerInterface $serializer): Response
    {
        $clientDetails = $clientRepository->find($client->getId());

        $json = $serializer->serialize($clientDetails, 'json', SerializationContext::create()->setGroups(array('clients:read')));

        $response = new JsonResponse($json, 200, [], true);
        return $response;
    }

    /**
     * 
     * List the selected client's customers.
     * 
     * @Route("/api/customers", name="api_client_customers", methods={"GET"})
     * 
     * @OA\Response(
     *     response=200,
     *     description="Returns a list of the customers attached to the selected",
     *     @OA\JsonContent(
     *         type="array",
     *         @OA\Items(ref=@Model(type=Client::class))
     *     )
     * )
     * 
     * @OA\Tag(name="Customers")
     * 
     * @Security(name="Bearer")
     * 
     */
    public function getClientCustomers(
        Request $request,
        EndUserRepository $endUserRepository,
        SerializerInterface $serializer): Response
    {
        // Select only the customers attached to the authentified client
        $endUsers = $endUserRepository->findBy(['Client' => $this->getUser()]);
        
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
     * @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="The field used to identify one customer.",
     *     @OA\Schema(type="string")
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
     * @Route("/api/customers", name="api_client_customer_new", methods={"POST"})
     * 
     * @OA\Response(
     *     response=201,
     *     description="Create a new customer attached to a selected client.",
     *     @OA\JsonContent(
     *         type="array",
     *         @OA\Items(ref=@Model(type=EndUser::class))
     *     )
     * )
     * 
     * @OA\Tag(name="Customers")
     * 
     * @Security(name="Bearer")
     */
    public function createCustomer(
        Request $request, 
        Client $client, 
        SerializerInterface $serializer, 
        EntityManagerInterface $manager,
        ValidatorInterface $validator
        )
    {

        // Get the information of the new Client from the POST request
        $jsonContent = $request->getContent();

        try {
            // Deserialize from json to be saved as an EndUser
            $newCustomer = $serializer->deserialize($jsonContent, EndUser::class, 'json');
            $newCustomer->setClient($client);

            // Make sure that the info are corrects
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
     * @Route("/api/customers/{id}", name="api_client_customer_delete", methods={"DELETE"})
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
     * @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="The field used to identify one customer.",
     *     @OA\Schema(type="string")
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
