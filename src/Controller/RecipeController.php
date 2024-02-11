<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Entity\Category;
use App\Entity\Ingredient;
use App\Entity\Measure;
use App\Entity\Step;
use App\Repository\RecipeRepository;
use App\Repository\MeasureRepository;
use App\Repository\CategoryRepository;
use App\Repository\IngredientRepository;
use App\Repository\StepRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RecipeController extends AbstractController
{
    // Création d'une nouvelle recette (association d'une catégorie existante seulement)
    #[Route('/api/create', name: 'app_recipe', methods:['POST'])]
    public function create(
    Request $Request, 
    EntityManagerInterface $em, 
    SerializerInterface $serializer,
    CategoryRepository $categoryRepository
    ): JsonResponse 
        {

            $recipe = $serializer->deserialize($Request->getContent(), Recipe::class, 'json');

            // Récupération de l'ensemble des données envoyées sous forme de tableau
            $content = $Request->toArray();

            // Récupération de l'idCategory.
            $idCategory = $content['idCategory'];

            // Si la category n'existe pas, alors null sera retourné.
            $recipe->addCategory($categoryRepository->find($idCategory));

            $em->persist($recipe);
            $em->flush();

            $jsonRecipe = $serializer->serialize($recipe, 'json', ['groups' => 'recipe']);

            return new JsonResponse($jsonRecipe, Response::HTTP_CREATED, [], true);
        }
    


    // Suppression d'une recette
    #[Route('/api/recipe/{id}', name: 'deleteRecipe', methods: ['DELETE'])]
    public function deleteRecipe(Recipe $recipe, EntityManagerInterface $em): JsonResponse 
    {
        $em->remove($recipe);
        $em->flush();

        return new JsonResponse(['message' => 'La recette a bien été supprimée.']);
    }

    // Modification d'une recette
    #[Route('/api/recipe/{id}', name:"updateRecipe", methods:['PUT'])]

    public function updateRecipe(
    Request $request, 
    SerializerInterface $serializer, 
    Recipe $currentRecipe, 
    EntityManagerInterface $em, 
    RecipeRepository $recipeRepository,
    CategoryRepository $categoryRepository,
    IngredientRepository $ingredientRepository): JsonResponse 
    {
        $updatedRecipe = $serializer->deserialize($request->getContent(), 
                Recipe::class, 
                'json', 
                [AbstractNormalizer::OBJECT_TO_POPULATE => $currentRecipe]);

        $content = $request->toArray();

        $idCategory = $content['idCategory'];

        // Association de plusieurs ingrédients à une recette
        if (isset($content['idIngredient']) && is_array($content['idIngredient'])) {
            foreach ($content['idIngredient'] as $idIngredient) {
                $updatedRecipe->addIngredient($ingredientRepository->find($idIngredient));
            }
        }

        $updatedRecipe->addCategory($categoryRepository->find($idCategory));
        
        $em->persist($updatedRecipe);
        $em->flush();

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
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
    public function getOneRecipe($id, 
    RecipeRepository $RecipeRepository, 
    SerializerInterface $serializer): JsonResponse
    {
        $recipe = $RecipeRepository->find($id);

        $JsonRecipe = $serializer->serialize($recipe,'json', ['groups' => 'recipe']);

        return new JsonResponse($JsonRecipe, Response::HTTP_OK, [], true);
    }

    
    

}
