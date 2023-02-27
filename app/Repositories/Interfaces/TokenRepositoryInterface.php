<?php

namespace App\Repositories\Interfaces;


interface TokenRepositoryInterface
{
    public function create(array $token_token_attributes);

    public function retrieve($access_token = null);

    public function update($access_token, array $token_attributes);

    public function delete($access_token);
    public function retrieveAll();
}