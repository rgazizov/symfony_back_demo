<?php

namespace App\Controller;

use App\Entity\Pet;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Swagger\Annotations as SWG;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class PetController extends AbstractController
{
    /**
     * @Route("/api/pets", name="pets", methods={"GET"})
     * @SWG\Get(
     *     path="/api/pets",
     *     summary="Get pets",
     *     description="Get pets",
     *     produces={"application/json"},
     *     @SWG\Response(
     *         response=200,
     *         description="Success",
     *     )
     * )     */
    public function pets()
    {
        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);
        $pets = $this->getDoctrine()->getRepository(Pet::class)->findAll();
        $json = $serializer->serialize($pets, 'json');

        return new Response($json);

    }

    /**
    * @Route("/api/pet/add", name="add_pet", methods={"POST"})
    * @SWG\Post(
    *     path="/api/pet/add",
    *     summary="Add a pet",
    *     description="Adding a pet via JSON",
    *     @SWG\Parameter(
    *         name="name",
    *         in="body",
    *         description="Pet name",
    *         @Model(type=Pet::class)
    *     ),
    *     produces={"application/json"},
    *     @SWG\Response(
    *         response=200,
    *         description="Success",
    *     )
    * )
    */
    public function add_pet(Request $request)
    {
        if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
            $data = json_decode($request->getContent(), true);
            $pet = new Pet();
            $pet->setName($data['name']);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($pet);
            $entityManager->flush();
            return $this->json([
                'status' => 'OK'
            ]);
        }
    }
}
