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
     * @Route("/api/phones", name="api_phones", methods = {"GET"})
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
     * @OA\Response(response="401",description="Error: Unauthorized.")
     * @OA\Response(response=500, description="Error: Internal error")
     * 
     * @OA\Tag(name="Phone")
     * 
     * @Security(name="Bearer")
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
     * @OA\Response(response="401",description="Error: Unauthorized.")
	 * @OA\Response(response="404",description="Error: Not found.")
     * @OA\Response(response=500, description="Error: Internal error")
     * 
     * @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="The unique identifier of one phone.",
     *     @OA\Schema(type="integer"),
     *     @OA\Examples(example="int", value="1",summary="An int value for example")
     * )
     * @OA\Tag(name="Phone")
     * 
     * @Security(name="Bearer")
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
