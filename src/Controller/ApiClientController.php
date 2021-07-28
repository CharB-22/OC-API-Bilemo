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

class ApiClientController extends AbstractController
{
    /**
     * @Route("/api/clients", name="api_clients_list", methods={"GET"})
     */
    public function getClientList(ClientRepository $clientRepository, SerializerInterface $serializer): Response
    {
        
        $clients = $clientRepository->findAll();
        
        $json = $serializer->serialize($clients, 'json', SerializationContext::create()->setGroups(array('clients:read')));

        $response = new JsonResponse($json, 200, [], true);
       
        return $response;
    }
    
    /**
     * @Route("/api/clients/{id}", name="api_client_details", methods={"GET"})
     */
    public function getClient(ClientRepository $clientRepository, Client $client, SerializerInterface $serializer): Response
    {
        $clientDetails = $clientRepository->find($client->getId());

        $json = $serializer->serialize($clientDetails, 'json', SerializationContext::create()->setGroups(array('clients:read')));

        $response = new JsonResponse($json, 200, [], true);
        return $response;
    }

    /**
     * @Route("/api/clients/{id}/customers", name="api_client_customers", methods={"GET"})
     */
    public function getClientCustomers(
        Client $client, 
        EndUserRepository $endUserRepository,
        SerializerInterface $serializer): Response
    {
        $endUsers = $endUserRepository->findBy(['Client' => $client->getId()]);

        $json = $serializer->serialize($endUsers, 'json', SerializationContext::create()->setGroups(array('customers:read')));
        $response = new JsonResponse($json, 200, [], true);

        return $response;
    }

    /**
     * @Route("/api/clients/{company}/customers/{lastName}", name="api_customers_details", methods={"GET"})
     */
    public function getCustomerDetails(
        EndUser $endUser,
        EndUserRepository $endUserRepository,
        SerializerInterface $serializer
    ): Response
    {
        $endUserDetails = $endUserRepository->findBy(['lastName' => $endUser->getLastName()]);

        $json = $serializer->serialize($endUserDetails, 'json', SerializationContext::create()->setGroups(array('customers:read')));

        $response = new JsonResponse($json, 200, [], true);

        return $response;
    }

    /**
     * @Route("/api/clients/{id}/customers", name="api_client_customer_new", methods={"POST"})
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
     * @Route("/api/clients/{username}/customers/{id}", name="api_client_customer_delete", methods={"DELETE"})
     */
    public function deleteCustomer(EndUser $endUser, EntityManagerInterface $manager)
    {
        // Remove the element from the database
        $manager->remove($endUser);
        $manager->flush();

        return $this->json([
            "status" => 204,
            "message" => "Utilisateur supprimé."
        ], 204);
        
    }
}
