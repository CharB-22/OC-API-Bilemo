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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

use Doctrine\ORM\EntityManagerInterface;

class ApiClientController extends AbstractController
{
    /**
     * @Route("/api/clients", name="api_clients_list", methods={"GET"})
     */
    public function getClientList(ClientRepository $clientRepository): Response
    {
        
        return $this->json($clientRepository->findAll(), 200, [], ['groups' => 'clients:read']);
    }
    
    /**
     * @Route("/api/clients/{id}", name="api_client_details", methods={"GET"})
     */
    public function getClient(ClientRepository $clientRepository, Client $client): Response
    {

        return $this->json($clientRepository->find($client->getId()), 200, [], ['groups' => 'clients:read']);
    }

    /**
     * @Route("/api/clients/{id}/customers", name="api_client_customers", methods={"GET"})
     */
    public function getClientCustomers(
        Client $client, 
        EndUserRepository $endUserRepository): Response
    {

        return $this->json($endUserRepository->findBy(['Client' => $client->getId()]), 200, [], ['groups' => 'customers:read']);
    }

    /**
     * @Route("/api/clients/{company}/customers/{lastName}", name="api_client_customers", methods={"GET"})
     */
    public function getCustomerDetails(
        EndUser $endUser,
        EndUserRepository $endUserRepository
    )
    {
        return $this->json($endUserRepository->findBy(['lastName' => $endUser->getLastName()]), 200, [], ['groups' => 'customers:read']);
    }

    /**
     * @Route("/api/clients/{id}/customers", name="api_client_customer_new", methods={"POST"})
     */
    public function createCustomer(Request $request, Client $client, SerializerInterface $serializer, EntityManagerInterface $manager)
    {

        // Get the information of the new Client from the POST request
        $jsonContent = $request->getContent();
        // Deserialize from json to be saved as an EndUser
        $newCustomer = $serializer->deserialize($jsonContent, EndUser::class, 'json');
        $newCustomer->setClient($client);

        // Push it to the database
        $manager->persist($newCustomer);
        $manager->flush();

        $data = [
            'status' => 201,
            'message' => 'L\'utilisateur a bien été ajouté'
        ];

        // Return a response to Postman
        return $this->json($data, 201, [], ['groups' => 'customer:read']);
    }

        /**
     * @Route("/api/clients/{name}/customers/{id}", name="api_client_customer_delete", methods={"DELETE"})
     */
    public function deleteCustomer(EndUser $endUser, EntityManagerInterface $manager)
    {
        // Remove the element from the database
        $manager->remove($endUser);
        $manager->flush();

        return new Response(null, 204);
    }
}
