<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Entity\Category;
use App\Entity\Ingredient;
use App\Repository\RecipeRepository;
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
    public function create(Request $Request, 
    EntityManagerInterface $em, 
    SerializerInterface $serializer, 
    CategoryRepository $categoryRepository, 
    IngredientRepository $ingredientRepository): JsonResponse
    {
        $recipe = $serializer->deserialize($Request->getContent(), Recipe::class, 'json');

        $content = $Request->toArray();

        $idCategory = $content['idCategory'];

        // Association de plusieurs ingrédients à une recette
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

    // Route qui renvoi la liste de toute les recettes (titre et description)
    #[Route('/api/recipes', name: 'all_recipes', methods: ['GET'])]
    public function getAllRecipe(RecipeRepository $RecipeRepository, 
    SerializerInterface $serializer): JsonResponse
    {
        $recipesList = $RecipeRepository->findAll();
        
        $jsonRecipesList = $serializer->serialize($recipesList, 'json', ['groups'=>'all_recipe']);
        
        return new JsonResponse($jsonRecipesList, Response::HTTP_OK, [], true);
    }

    // Route qui renvoi une recette
    #[Route('/api/recipe/{id}', name: 'one_recipe', methods: ['GET'])]
    public function getOneRecipe($id, RecipeRepository $RecipeRepository, SerializerInterface $serializer): JsonResponse
    {
        $recipe = $RecipeRepository->find($id);

        $JsonRecipe = $serializer->serialize($recipe,'json', ['groups' => 'recipe']);

        return new JsonResponse($JsonRecipe, Response::HTTP_OK, [], true);
    }

    // Route qui permet la recherche de recettes (barre de recherche)

    #[Route('/api/search/recipe', name: 'search_recipe', methods: ['GET'])]

    public function searchRecipe(SerializerInterface $serializer,
    Request $Request, EntityManagerInterface $em): JsonResponse
    {
        $keyWord = $Request->query->get('term');
    }
    

}
