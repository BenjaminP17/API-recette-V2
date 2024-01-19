<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Recipe;
use App\Repository\RecipeRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{
    //Creation d'un utilisateur
    #[Route('/api/login', name: 'app_user', methods:['POST'])]
    public function register(Request $request,
    UserPasswordHasherInterface $passwordHasher,
    EntityManagerInterface $em,
    UserRepository $userRepository): JsonResponse
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

        $em->persist($user);
        $em->flush();

        return $this->json($user, 200, []);


    }

    //Ajout d'une recette en favori
    #[Route('/api/favorite/add/{id}', name: 'app_favoris_add', methods:['GET'])]
    public function addFavorite (
        Request $Request, RecipeRepository $RecipeRepository,
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
}