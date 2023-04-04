<?php

namespace App\Services;

use App\Entity\Token;
use App\Entity\User;

interface ApiAuthenticatorInterface
{
    public function authByCredentials(string $login, string $password): ?Token;

    public function authByToken(string $token): ?User;

    public function makeToken(): string;
}