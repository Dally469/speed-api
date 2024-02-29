<?php

namespace App\Controllers\Api\Web;


use App\Controllers\BaseController;
use App\Models\User as UserModel;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;

class AuthController extends BaseController
{

    /**
     * Logs in a user by validating their email address and password.
     *
     * @return ResponseInterface Returns a response containing a JWT for the user.
     * @throws \JsonException
     */
    public function login()
    : ResponseInterface
    {
        $rules = [
            'email'    => 'required|max_length[50]|valid_email',
            'password' => 'required|max_length[255]|validateUser[email, password]'
        ];
        $errors = [
            'password' => [
                'validateUser' => 'Invalid login credentials provided'
            ]
        ];
        $input = $this->getRequestInput($this->request);
        if (!$this->validateRequest($input, $rules, $errors)) {
            return $this->getResponse([
                'status' => ResponseInterface::HTTP_BAD_REQUEST,
                'message' => $this->validator->getErrors()['password']
            ], ResponseInterface::HTTP_BAD_REQUEST);
        }
        return $this->getJWTForUser($input[ 'email' ]);
    }

    /**
     * Retrieves a JWT (JSON Web Token) for a user based on their email address.
     *
     * @param string $emailAddress The email address of the user.
     * @param int $responseCode The HTTP response code to be returned.
     * @return ResponseInterface Returns a response containing the generated JWT and user data.
     */
    private function getJWTForUser(string $emailAddress, int $responseCode = ResponseInterface::HTTP_OK)
    : ResponseInterface
    {
        try {
            $model = new UserModel();
            $user = $model->findUserByEmailAddress($emailAddress);
            unset($user[ 'password' ]);

            helper('jwt');

            return $this->getResponse([
                'status'       => $responseCode,
                'message'      => 'User authenticated successfully',
                'data'         => array_merge(['access_token' => getSignedJWTForUser($emailAddress)], $user),
            ]);
        } catch (Exception $exception) {
            return $this->getResponse([
                'error' => $exception->getMessage(),
            ], $responseCode
            );
        }
    }
}