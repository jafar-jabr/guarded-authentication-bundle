<?php
/**
 * Created by PhpStorm.
 * User: Jafar Jabr
 * Date: 12/8/2017
 * Time: 12:53 PM
 */

namespace Jafar\Bundle\GuardedAuthenticationBundle\EventListener;

use Jafar\Bundle\GuardedAuthenticationBundle\Api\ApiProblem;
use Jafar\Bundle\GuardedAuthenticationBundle\Api\ApiResponseFactory;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class ApiExceptionSubscriber implements EventSubscriberInterface
{

    private $debug;
    private $responseFactory;

    public function __construct($debug, ApiResponseFactory $responseFactory)
    {
        $this->debug = $debug;
        $this->responseFactory = $responseFactory;
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        if (strpos($event->getRequest()->getPathInfo(), '/api') !== 0) {
            return;
        }
        $e = $event->getException();
        $statusCode = $e instanceof HttpExceptionInterface ? $e->getStatusCode() : 200;
        if ($this->debug && $statusCode >= 500) {
            return;
        }
//        if($e instanceof ApiProblemException){
//            $apiProblem = $e->getApiProblem();
//        }else{
//            $apiProblem = new ApiProblem($statusCode);
//        }
        $apiProblem = new ApiProblem($statusCode);
        if ($e instanceof HttpExceptionInterface) {
            $apiProblem->set('details', $e->getMessage());
        }
        $response = $this->responseFactory->createResponse($apiProblem);
        $event->setResponse($response);
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException'
        ];
    }
}