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
use Symfony\Component\Serializer\SerializerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;

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
     * @Route("/api/clients/{company}/customers/{lastName}", name="api_customers_details", methods={"GET"})
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
