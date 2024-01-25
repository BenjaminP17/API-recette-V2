<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Recipe;
use App\Entity\Review;
use App\Repository\UserRepository;
use App\Repository\RecipeRepository;
use App\Repository\ReviewRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{
    //Creation d'un utilisateur
    #[Route('/api/login', name: 'app_user', methods:['POST'])]
    public function register(Request $request,
    UserPasswordHasherInterface $passwordHasher,
    EntityManagerInterface $em,
    UserRepository $userRepository,
    SerializerInterface $serializer,
    ValidatorInterface $validator): JsonResponse
    {
        $user = new User ();

        $data = json_decode($request->getContent(), true);

        $email = $data['email'];
        $password = $data['password'];
        $nickname = $data['nickname'];

        $hashedPassword = $passwordHasher->hashPassword(
            $user,
            $password
        );

        $user->setEmail($email);
        $user->setPassword($hashedPassword);
        $user->setRoles(['ROLE_USER']);
        $user->setNickname($nickname);

        // On vérifie les erreurs
        $errors = $validator->validate($user);

        if ($errors->count() > 0) {
            return new JsonResponse($serializer->serialize($errors, 'json'), JsonResponse::HTTP_BAD_REQUEST, [], true);
        }

        $em->persist($user);
        $em->flush();

        return $this->json($user, 200, []);


    }

    //Route qui liste tous les utilisateurs
    #[Route('/api/list/users', name: 'app_users_list', methods:['GET'])]
    public function list (
        UserRepository $UserRepository,
        SerializerInterface $serializer
    ) : JsonResponse
    {
        $usersList = $UserRepository->findAll();

        $jsonUsersList = $serializer->serialize($usersList, 'json', ['groups'=>'all_users']);
        
        return new JsonResponse($jsonUsersList, Response::HTTP_OK, [], true);
    }

    //Ajout d'une recette en favori
    // pour cette route, il faut fournir le token, et renseigner l'id d'une recette en fin d'url
    #[Route('/api/favorite/add/{id}', name: 'app_favoris_add', methods:['GET'])]
    public function addFavorite (
        Request $Request, 
        RecipeRepository $RecipeRepository,
        EntityManagerInterface $em
    ) : JsonResponse
    {
        $user = $this->getUser();

        if (!$user) {
            $this->json([
                'message' => 'Utilisateur non connecté. Fournir le Token JWT.'
            ], Response::HTTP_BAD_REQUEST);
        }

        $RecipeId = $Request->get('id');

        $recipe = $RecipeRepository->findOneById($RecipeId);

        if (!$recipe) {
            $recipe = new Recipe();

            $recipe->setId($RecipeId);

            $em->persist($recipe);
            $em->flush();
        }

        $user->addRecipe($recipe);

        $em->flush();

        return $this->json([
            "La recette '$RecipeId' a bien été ajoutée aux favoris de l'utilisateur",
        ], 201);
    }

    // Route qui permet l'ajout d'un commentaire d'un user sur une recette
    #[Route('/api/review/add', name: 'review_add', methods:['POST'])]

    public function addReview(
        Request $request,
        SerializerInterface $serializer,
        EntityManagerInterface $em,
        RecipeRepository $recipeRepository
    ): JsonResponse

    {
        // On récupère l'utiliseur courant grâce au JWT fourni dans la requête, dans le header Authorization
        // attention ce header doit avoir le format : `Bearer {valeur du token}`
        $user = $this->getUser();

        // Si on n'a pas d'user, on renvoit une erreur
        // impossible d'ajouter une review sans utilisateur qui la publie ..
        if (!$user) {
            $this->json([
                'message' => 'Utilisateur non connecté. Fournir le JWT.'
            ], Response::HTTP_BAD_REQUEST);
        }

        // On récupère le contenu de la requête
        $jsonContent = $request->getContent();
        // On décode pour pouvoir récupérer les valeurs au format tableau associatif PHP
        $data = json_decode($jsonContent, true);

        // On récupère l'id d'une recette;
        // Si n'existe pas, la créer.
        if (!isset($data['id']) || empty($data['id'])) {
            return $this->json([
                'message' => 'La propriété id est manquante.'
            ], Response::HTTP_BAD_REQUEST);
        }

        $recipe = $recipeRepository->findOneById($data['id']);

        if (!$recipe) {
            $recipe = new Recipe();

            $recipe->setId($data['id']);

            $em->persist($recipe);
            $em->flush();
        }

        $newReview = $serializer->deserialize($jsonContent, Review::class, 'json');

        $newReview->setRecipe($recipe);
        $newReview->setUser($user);

        $em->persist($newReview);
        $em->flush();

        return $this->json($newReview, Response::HTTP_CREATED, [], ['groups'=>'review_recipe']);
    }
}