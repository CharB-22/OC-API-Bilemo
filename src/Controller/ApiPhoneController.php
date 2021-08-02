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
     * 
     * @OA\Response(response="401",description="JWT Token not found.")
	 * @OA\Response(response="404",description="Not route found.")
     * 
     * @OA\Tag(name="Phone")
     * 
     * 
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
     *     description="Returns the details of a selected phone.",
     *     @OA\JsonContent(
     *         type="array",
     *         @OA\Items(ref=@Model(type=Phone::class))
     *     )
     * )
     * 
     * @OA\Response(response="401",description="JWT Token not found.")
	 * @OA\Response(response="404",description="Not route found.")
     * 
     * @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="The unique identifier of one phone.",
     *     @OA\Schema(type="string")
     * )
     * @OA\Tag(name="Phone")
     * 
     *
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
