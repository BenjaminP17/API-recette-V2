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
    // Création d'une nouvelle recette
    //!! Gérer la liaison avec ingredient et mesure
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

    // Supprimer une recette
    #[Route('/api/recipe/{id}', name: 'deleteRecipe', methods: ['DELETE'])]
    public function deleteRecipe(Recipe $recipe, EntityManagerInterface $em): JsonResponse 
    {
        $em->remove($recipe);
        $em->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    // Création d'un nouvel ingrédient
    #[Route('/api/create/ingredient', name: 'create_ingredient', methods:['POST'])]
    public function createIngredient(Request $Request,
    EntityManagerInterface $em, 
    SerializerInterface $serializer, 
    IngredientRepository $ingredientRepository): JsonResponse
    {
        $ingredient = $serializer->deserialize($Request->getContent(), Ingredient::class, 'json');

        $em->persist($ingredient);
        $em->flush();

        $jsonIngredient = $serializer->serialize($ingredient, 'json', ['groups' => 'ingredient']);

        return new JsonResponse($jsonIngredient, Response::HTTP_CREATED, [], true);

    }

    // liste de toute les recettes (titre et description)
    #[Route('/api/recipes', name: 'all_recipes', methods: ['GET'])]
    public function getAllRecipe(RecipeRepository $RecipeRepository, 
    SerializerInterface $serializer): JsonResponse
    {
        $recipesList = $RecipeRepository->findAll();
        
        $jsonRecipesList = $serializer->serialize($recipesList, 'json', ['groups'=>'all_recipe']);
        
        return new JsonResponse($jsonRecipesList, Response::HTTP_OK, [], true);
    }

    // Une recette
    #[Route('/api/recipe/{id}', name: 'one_recipe', methods: ['GET'])]
    public function getOneRecipe($id, RecipeRepository $RecipeRepository, SerializerInterface $serializer): JsonResponse
    {
        $recipe = $RecipeRepository->find($id);

        $JsonRecipe = $serializer->serialize($recipe,'json', ['groups' => 'recipe']);

        return new JsonResponse($JsonRecipe, Response::HTTP_OK, [], true);
    }
    //!!TODO
    // Route qui permet la recherche de recettes (barre de recherche)

    // #[Route('/api/search/recipe', name: 'search_recipe', methods: ['GET'])]

    // public function searchRecipe(SerializerInterface $serializer,
    // Request $Request, EntityManagerInterface $em): JsonResponse
    // {
    //     $keyWord = $Request->query->get('term');
    // }
    

}
