<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Entity\Category;
use App\Repository\RecipeRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SearchController extends AbstractController
{
    // TODO 
    // Route qui permet la recherche de recettes (barre de recherche)

    #[Route('/api/recipe/search', methods: ['GET'])]

    public function searchRecipe(Request $request): JsonResponse
    {
        $keyword = $request->query->get('q');

    }

    // Recherche par catégorie
    #[Route('/api/recipe/category/{id}', name: 'category', methods: ['GET'])]
    public function getCategory(
        Category $category,
        RecipeRepository $recipeRepository,
        SerializerInterface $serializer
    ): JsonResponse
    {
        // Vérifier si la catégorie existe
        if (!($category)) {
        return new JsonResponse(['error' => 'Catégorie non trouvée'], Response::HTTP_NOT_FOUND);
        }  
        
        $recipeCategory = $category->getRecipes();

        $data = [
            'category' => $category,
            'recipe'=> $recipeCategory
        ];


        $jsonRecipe = $serializer->serialize($data, 'json', ['groups' => 'recipe']);

        return new JsonResponse($jsonRecipe, Response::HTTP_OK, [], true);
    }
}
