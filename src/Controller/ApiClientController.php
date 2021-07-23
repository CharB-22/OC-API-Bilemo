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
}
