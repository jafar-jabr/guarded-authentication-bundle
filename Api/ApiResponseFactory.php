<?php
/*
 * This file is part of the Guarded Authentication package.
 *
 * (c) Jafar Jabr <jafaronly@yahoo.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jafar\Bundle\GuardedAuthenticationBundle\Api;

use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @author Jafar Jabr <jafaronly@yahoo.com>
 * Date: 11/02/2017
 */

final class ApiResponseFactory
{
    public function createResponse(ApiProblem $apiProblem)
    {
        $data = $apiProblem->toArray();
        if ($data['type'] != 'about:blank') {
            $data['type'] = 'http://localhost/just_url/web/errors#' . $data['type'];
        }
        $response = new JsonResponse($data, $apiProblem->getStatusCode());
        $response->headers->set('Content-Type', 'application/problem+json');
        return $response;
    }
}
