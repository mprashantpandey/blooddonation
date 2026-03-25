<?php

namespace App\Contracts;

interface FirebaseIdTokenVerifier
{
    /**
     * @return array{uid:string, phone_number?:string|null}
     */
    public function verify(string $idToken): array;
}

