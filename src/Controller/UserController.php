<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\ApiResponse;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/user', methods:"GET")]
    public function getInfos(Request $request, ApiResponse $apiResponse): Response
    {
        $params = [
            "user_id" => [
                "type" => "integer"
            ],
        ];
        $apiResponse->setParams($params);
        $response = $apiResponse->isParamsExistAndCorrectType($request);
        if ($apiResponse->hasError) {
            return $this->json($response, 400, ['Content-Type' => 'application/json']);
        }
        $userId = $response["user_id"];
        $infos = $this->em->getRepository(User::class)->findInfos($userId);
        return $this->json($infos);
    }

    #[Route('/user/{id}', methods:"POST")]
    public function update($id, Request $request, ApiResponse $apiResponse, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = $this->em->getRepository(User::class)->find($id);

        if(empty($user)) {
            return $this->json("Doesn't exist", 404);
        }
        $params = [
            "last_name" => [
                "type" => "string",
                "required" => false
            ],
            "first_name" => [
                "type" => "string",
                "required" => false
            ],
            "email" => [
                "type" => "string",
                "required" => false
            ],
            "current_password" => [
                "type" => "string"
            ],
            "new_password" => [
                "type" => "string",
                "required" => false
            ],
        ];
        $apiResponse->setParams($params);
        $response = $apiResponse->isBodyExistAndCorrectType($request);
        if ($apiResponse->hasError) {
            return $this->json($response, 400, ['Content-Type' => 'application/json']);
        }

        $currentPassword = $response["current_password"];

        if(!$passwordHasher->isPasswordValid($user, $currentPassword)) {
            return $this->json("Invalid password", 404);
        }

        $email = $response["email"] ?? null;
        if($email) $user->setEmail($email);
        $firstName = $response["first_name"] ?? null;
        if($firstName) $user->setFirstName($firstName);
        $lastName = $response["last_name"] ?? null;
        if($lastName) $user->setLastName($lastName);
        $newPassword = $response["new_password"] ?? null;
        if($newPassword) {
            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $newPassword
            );
            $user->setPassword($hashedPassword);
        }

        $user->setUpdatedAt(New DateTimeImmutable());
        $this->em->persist($user);
        $this->em->flush();

        $infos = $this->em->getRepository(User::class)->findInfos($id);
        return $this->json($infos);
    }
}
