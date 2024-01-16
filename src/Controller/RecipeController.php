<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Entity\Category;
use App\Entity\Ingredient;
use App\Repository\CategoryRepository;
use App\Repository\IngredientRepository;
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
    public function create(Request $Request, EntityManagerInterface $em, SerializerInterface $serializer, CategoryRepository $categoryRepository, IngredientRepository $ingredientRepository): JsonResponse
    {
        $recipe = $serializer->deserialize($Request->getContent(), Recipe::class, 'json');

        $content = $Request->toArray();
        
        $idCategory = $content['idCategory'];

        // Modification pour prendre en charge plusieurs idIngredients
        if (isset($content['idIngredient']) && is_array($content['idIngredient'])) {
            foreach ($content['idIngredient'] as $idIngredient) {
                $recipe->addIngredient($ingredientRepository->find($idIngredient));
            }
        }

        $recipe->addCategory($categoryRepository->find($idCategory));

        $em->persist($recipe);
        $em->flush();

        $jsonRecipe = $serializer->serialize($recipe, 'json', ['groups' => 'recipe']);

        return new JsonResponse($jsonRecipe, Response::HTTP_CREATED, [], true);
    }
}
