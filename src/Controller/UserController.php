<?php

namespace App\Controller;

use App\Entity\User;
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
}