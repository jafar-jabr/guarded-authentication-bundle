<?php
namespace Jafar\Bundle\GuardedAuthenticationBundle\Api;
/**
 * Created by PhpStorm.
 * User: Jafar Jabr
 * Date: 12/8/2017
 * Time: 2:50 AM
 */
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class ApiResponseFactory
 */
final class ApiResponseFactory
{
    public function createResponse(ApiProblem $apiProblem)
    {
        $data = $apiProblem->toArray();
        if ($data['type'] != 'about:blank') {
            $data['type'] = 'http://localhost/mcd-crm/web/errors#' . $data['type'];
        }
        $response = new JsonResponse($data, $apiProblem->getStatusCode());
        $response->headers->set('Content-Type', 'application/problem+json');
        return $response;
    }
}
