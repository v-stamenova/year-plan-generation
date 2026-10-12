<?php

namespace App;

use App\Controllers\YearPlanController;
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
    }
}
