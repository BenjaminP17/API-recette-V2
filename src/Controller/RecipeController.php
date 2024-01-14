<?php

namespace App\Controller;

use App\Entity\Recipe;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RecipeController extends AbstractController
{
    #[Route('/api/create', name: 'app_recipe', methods:['POST'])]
    public function create(Request $Request, EntityManagerInterface $em, SerializerInterface $serializer): JsonResponse
    {
        //!! TODO Gérer données "unique" sur category et ingredient
        $recipe = $serializer->deserialize($Request->getContent(), Recipe::class, 'json');
        
        $em->persist($recipe);
        $em->flush();

        $jsonRecipe = $serializer->serialize($recipe, 'json');

        return new JsonResponse($jsonRecipe, Response::HTTP_CREATED, [], true);
    }
}
