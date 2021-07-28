<?php

namespace App\Controller;

use App\Repository\PhoneRepository;
use App\Entity\Phone;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use JMS\Serializer\SerializerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;


class ApiPhoneController extends AbstractController
{
    /**
     * List the phone selection available at Bilemo.
     * 
     * @Route("/api/phones", name="api_phones_list", methods = {"GET"})
     * 
     * @OA\Response(
     *     response=200,
     *     description="Returns the list of phones.",
     *     @OA\JsonContent(
     *         type="array",
     *         @OA\Items(ref=@Model(type=Phone::class))
     *     )
     * )
     * @OA\Tag(name="Phone")
     * @Security(name="Bearer")
     */
    public function getPhoneList(PhoneRepository $phoneRepository, SerializerInterface $serializer): Response
    {
        $phones = $phoneRepository->findAll();

        $json = $serializer->serialize($phones, 'json');

        $response = new JsonResponse($json, 200, [], true);
       
        return $response;
    }

    /**
     * Give the details on a selected phone.
     * 
     * @Route("/api/phones/{id}", name="api_phone_details", methods = {"GET"})
     * 
     * @OA\Response(
     *     response=200,
     *     description="Returns the details of a phone object.",
     *     @OA\JsonContent(
     *         type="array",
     *         @OA\Items(ref=@Model(type=Phone::class))
     *     )
     * )
     * @OA\Tag(name="Phone")
     * 
     * @Security(name="Bearer")
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
