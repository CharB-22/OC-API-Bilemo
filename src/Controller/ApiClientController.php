<?php

namespace App\Controller;

use App\Entity\Client;
use App\Repository\ClientRepository;
use App\Entity\EndUser;
use App\Repository\EndUserRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
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
}
