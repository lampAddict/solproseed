<?php

namespace AppBundle\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Router;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;

class AccessDeniedHandler implements AccessDeniedHandlerInterface
{
    protected $router;

    public function __construct(Router $router ){
        $this->router = $router;
    }

    public function handle(Request $request, AccessDeniedException $accessDeniedException)
    {
        $session = $request->getSession();

        $session->getFlashBag()->add('warning', 'Доступ запрещён.');

        return new RedirectResponse($this->router->generate('mainpage'), 302);
    }
}