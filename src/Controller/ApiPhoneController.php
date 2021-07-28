<?php

namespace App\Controller;

use App\Repository\PhoneRepository;
use App\Entity\Phone;
use JMS\Serializer\Serializer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use JMS\Serializer\SerializerInterface;


class ApiPhoneController extends AbstractController
{
    /**
     * @Route("/api/phones", name="api_phones_list", methods = {"GET"})
     */
    public function getPhoneList(PhoneRepository $phoneRepository, SerializerInterface $serializer): Response
    {
        $phones = $phoneRepository->findAll();

        $json = $serializer->serialize($phones, 'json');

        $response = new JsonResponse($json, 200, [], true);
       
        return $response;
    }

    /**
     * @Route("/api/phones/{id}", name="api_phone_details", methods = {"GET"})
     */
    public function getPhoneDetails(PhoneRepository $phoneRepository, 
    Phone $phone, 
    SerializerInterface $serializer
    ): Response
    {
        $phoneDetails = $phoneRepository->find($phone->getId());

        $json = $serializer->serialize($phoneDetails, 'json');

        $response = new JsonResponse($json, 200, [], true);
       
        return $response;
    }
}
