<?php

namespace App\Services;

use App\Entity\Token;
use App\Entity\User;
use App\Repository\TokenRepository;
use App\Repository\UserRepository;

class AuthService implements ApiAuthenticatorInterface
{
    protected $tokenRepository;

    protected $userRepository;

    protected $expireTime;

    /**
     * @param TokenRepository $tokenRepository
     * @param UserRepository $userRepository
     * @param int $expireTime seconds
     */
    public function __construct(
        TokenRepository $tokenRepository,
        UserRepository $userRepository,
        int $expireTime = 86400
    ) {
        $this->tokenRepository = $tokenRepository;
        $this->userRepository = $userRepository;
        $this->expireTime = $expireTime;
    }

    /**
     * @param string $login
     * @param string $password
     * @return Token|null
     */
    public function authByCredentials(string $login, string $password): ?Token
    {
        $user = $this->userRepository->findOneByCredentials($login, $password);

        if ($user) {
            $token = new Token();
            $token->setToken($this->makeToken());
            $token->setExpiredAt(new \DateTimeImmutable("+{$this->expireTime} seconds"));
            $token->setCreatedAt(new \DateTimeImmutable());
            $token->setUpdatedAt(new \DateTimeImmutable());
            $user->addToken($token);

            $this->userRepository->save($user, true);

            return $token;
        }

        // TODO throw Exception
        return null;
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function makeToken(): string
    {
        return hash('sha256', random_bytes(60));
    }

    /**
     * @param string $token
     * @return User|null
     */
    public function authByToken(string $token): ?User
    {
        $currentDate = new \DateTimeImmutable();

        $token = $this->tokenRepository->findOneBy(['token' => $token]);
        if ($token && $token->getExpiredAt() > $currentDate) {
            return $token->getUser();
        }

        if ($token) {
            $this->tokenRepository->remove($token, true);
        }

        return null;
    }
}