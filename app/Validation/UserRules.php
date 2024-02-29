<?php

namespace App\Validation;

use App\Models\User as UserModel;
use Exception;

class UserRules
{
    public function validateUser(string $str, string $fields, array $data)
    : bool
    {
        try {
            $model = new UserModel();
            $user = $model->findUserByEmailAddress($data[ 'email' ]);
            return password_verify($data[ 'password' ], $user[ 'password' ]);
        } catch (Exception $e) {
            log_message('error', $e->getMessage());
            return false;
        }
    }
}