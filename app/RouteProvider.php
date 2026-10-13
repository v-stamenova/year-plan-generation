<?php

namespace App;

use App\Controllers\YearPlanController;
use App\Controllers\YearTemplateController;
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
        $router->addRoute('GET', '/', [$container->get(YearPlanController::class), 'index']);
        $router->addRoute('POST', '/schedule/update', [$container->get(YearPlanController::class), 'update']);
        $router->addRoute('POST', '/schedule/delete', [$container->get(YearPlanController::class), 'delete']);
        $router->addRoute('GET', '/print', [$container->get(YearPlanController::class), 'print']);
        $router->addRoute('GET', '/year-plan', [$container->get(YearTemplateController::class), 'index']);
    }
}
