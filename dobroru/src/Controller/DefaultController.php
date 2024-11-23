<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
// use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class DefaultController extends AbstractController
{
    #[Route('/stats', name: 'route_stats')]
    public function stats(): Response
    {
        // $routeName = $request->attributes->get('_route');
        // $routeParameters = $request->attributes->get('_route_params');
        // $allAttributes = $request->attributes->all();

        $json = [];
        return new Response(json_encode($json));
    }
}
