<?php
/*
 * This file is part of the Guarded Authentication package.
 *
 * (c) Jafar Jabr <jafaronly@yahoo.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jafar\Bundle\GuardedAuthenticationBundle\Api\ApiResponse;

use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class ApiResponseFactory
 *
 * @author Jafar Jabr <jafaronly@yahoo.com>
 */
final class ApiResponseFactory
{
    public function createResponse(ApiProblem $apiProblem)
    {
        $data = $apiProblem->toArray();
        if ('about:blank' != $data['type']) {
            $data['type'] = 'http://localhost/just_url/web/errors#'.$data['type'];
        }
        $response = new JsonResponse($data, $apiProblem->getStatusCode());
        $response->headers->set('Content-Type', 'application/problem+json');

        return $response;
    }
}
