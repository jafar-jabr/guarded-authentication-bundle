<?php
/*
 * This file is part of the Guarded Authentication package.
 *
 * (c) Jafar Jabr <jafaronly@yahoo.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jafar\Bundle\GuardedAuthenticationBundle\EventListener;

use Jafar\Bundle\GuardedAuthenticationBundle\Api\ApiProblem;
use Jafar\Bundle\GuardedAuthenticationBundle\Api\ApiResponseFactory;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * @author Jafar Jabr <jafaronly@yahoo.com>
 * Date: 11/02/2017
 */
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
