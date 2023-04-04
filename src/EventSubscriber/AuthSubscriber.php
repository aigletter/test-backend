<?php

namespace App\EventSubscriber;

use App\Services\AuthService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class AuthSubscriber implements EventSubscriberInterface
{
    protected $authPath;

    protected $authService;

    /**
     * @param AuthService $authService
     * @param string $authPath
     */
    public function __construct(AuthService $authService, string $authPath)
    {
        $this->authService = $authService;
        $this->authPath = $authPath;
    }

    /**
     * @return array[]
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 100],
        ];
    }

    /**
     * @param RequestEvent $event
     * @return void
     */
    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        if ($request->getPathInfo() === $this->authPath) {
            return;
        }

        if (!$request->headers->has('Authorization')) {
            $event->setResponse(new Response('', 401));
            return;
        }

        $segments = explode(' ', $request->headers->get('Authorization'));
        if (count($segments) !== 2 || $segments[0] !== 'Bearer' || empty($segments[1])) {
            $event->setResponse(new Response('', 401));
            return;
        }

        $user = $this->authService->authByToken($segments[1]);

        if (!$user) {
            $event->setResponse(new Response('', 401));
        }
    }
}
