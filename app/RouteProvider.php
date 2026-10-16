<?php

namespace App;

use App\Controllers\YearPlanController;
use App\Controllers\TemplateController;
use Framework\Router;
use Framework\RouteProviderInterface;
use Framework\ServiceContainer;

class RouteProvider implements RouteProviderInterface
{
    /**
     * @throws \Exception
     */
    public function register(Router $router, ServiceContainer $container): void
    {
        $router->addRoute('GET', '/year-plan', [$container->get(YearPlanController::class), 'index']);
        $router->addRoute('GET', '/', [$container->get(YearPlanController::class), 'index']);
        $router->addRoute('GET', '/year-plan/item/(?<id>\d+)', [$container->get(YearPlanController::class), 'item']);
        $router->addRoute('GET', '/year-plan/(?<id>\d+)', [$container->get(YearPlanController::class), 'show']);
        $router->addRoute('POST', '/year-plan/(?<id>\d+)/edit', [$container->get(YearPlanController::class), 'updateBlock']);
        $router->addRoute('POST', '/year-plan/(?<id>\d+)/update', [$container->get(YearPlanController::class), 'update']);
        $router->addRoute('POST', '/year-plan/(?<id>\d+)/delete', [$container->get(YearPlanController::class), 'delete']);
        $router->addRoute('GET', '/year-plan/(?<id>\d+)/print', [$container->get(YearPlanController::class), 'print']);

        $router->addRoute('GET', '/template', [$container->get(TemplateController::class), 'index']);
        $router->addRoute('GET', '/template/(?<id>\d+)', [$container->get(TemplateController::class), 'show']);
        $router->addRoute('POST', '/template/(?<id>\d+)/update', [$container->get(TemplateController::class), 'update']);
        $router->addRoute('POST', '/template/(?<id>\d+)/delete', [$container->get(TemplateController::class), 'delete']);
        $router->addRoute('GET', '/template/(?<id>\d+)/print', [$container->get(TemplateController::class), 'print']);

    }
}
