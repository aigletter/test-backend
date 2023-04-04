<?php

namespace App\Controller;

use App\Services\AuthService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class AuthController extends AbstractController
{
    #[Route('api/auth', name: 'auth')]
    public function auth(Request $request, AuthService $service)
    {
        $data = json_decode($request->getContent());

        if ($token = $service->authByCredentials($data->login, $data->password)) {
            return new JsonResponse([
                'auth_token' => $token->getToken(),
                'expired_at' => $token->getExpiredAt()->format('Y-m-d H:i:s'),
            ]);
        }

        throw new AuthenticationException('Not authenticated', 401);
    }
}