<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Entity\Category;
use App\Repository\CategoryRepository;
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
    public function create(Request $Request, EntityManagerInterface $em, SerializerInterface $serializer, CategoryRepository $categoryRepository): JsonResponse
    {
        //!! TODO Gérer données "unique" sur category et ingredient
        $recipe = $serializer->deserialize($Request->getContent(), Recipe::class, 'json');

        // Récupération de l'ensemble des données envoyées sous forme de tableau
        $content = $Request->toArray();

        // Récupération de l'idAuthor. S'il n'est pas défini, alors on met -1 par défaut.
        $idCategory = $content['idCategory'];

        // On cherche l'auteur qui correspond et on l'assigne au livre.
        // Si "find" ne trouve pas l'auteur, alors null sera retourné.
        $recipe->addCategory($categoryRepository->find($idCategory));
        
        $em->persist($recipe);
        $em->flush();

        $jsonRecipe = $serializer->serialize($recipe, 'json', ['groups' => 'recipe']);

        return new JsonResponse($jsonRecipe, Response::HTTP_CREATED, [], true);
    }
}
