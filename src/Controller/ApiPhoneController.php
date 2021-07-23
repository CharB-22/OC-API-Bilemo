<?php

namespace App\Controller;

use App\Repository\PhoneRepository;
use App\Entity\Phone;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;


class ApiPhoneController extends AbstractController
{
    /**
     * @Route("/api/phones", name="api_phones_list", methods = {"GET"})
     */
    public function getPhoneList(PhoneRepository $phoneRepository): Response
    {
        return $response = $this->json($phoneRepository->findAll(), 200, [], []);
    }

    
}