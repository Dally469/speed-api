<?php

namespace App\Filters;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;
use Exception;

class JWTAuthenticationFilter implements FilterInterface
{
    use ResponseTrait;

    /**
     * @inheritDoc
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $authenticationHeader = $request->getServer('HTTP_AUTHORIZATION');

        try {
            helper('jwt');
            $encodedToken = getJWTFromRequest($authenticationHeader);
            validateJWTFromRequest($encodedToken);
            return $request;

        } catch (Exception $e) {
            return Services::response()->setJSON([
                'status'  => ResponseInterface::HTTP_UNAUTHORIZED,
                'message' => 'Authentication required!'
            ])->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * @inheritDoc
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}