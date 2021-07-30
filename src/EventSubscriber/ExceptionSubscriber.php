<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ExceptionSubscriber implements EventSubscriberInterface
{
    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();

        $message = $exception->getMessage();

        $response = new JsonResponse($message);

        if ($exception instanceof HttpException)
        {
            $data = [
                'status' => $exception->getStatusCode(),
                'message' => $message
            ];

            $response = new JsonResponse($data);
        }
        else
        {
            $response->setStatusCode(500);
        }
        
        $event->setResponse($response);
    }

    public static function getSubscribedEvents()
    {
        return [
            'kernel.exception' => 'onKernelException',
        ];
    }
}
