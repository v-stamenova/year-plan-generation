<?php

namespace App;

use App\Controllers\HomeController;
use App\Controllers\ProjectController;
use App\Controllers\TagController;
use App\Controllers\TaskController;
use App\Controllers\YearPlanController;
use App\Repositories\PlanItemRepository;
use App\Repositories\ProjectRepository;
use App\Repositories\ProjectRepositoryInterface;
use App\Repositories\TagRepository;
use App\Repositories\TaskRepository;
use App\Repositories\TaskRepositoryInterface;
use App\Repositories\YearPlanRepository;
use Exception;
use Framework\Database;
use Framework\ResponseFactory;
use Framework\ServiceContainer;
use Framework\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    /**
     * @throws Exception
     */
    public function register(ServiceContainer $container): void
    {
        $database = $container->get(Database::class);

        $yearPlanRepository = new YearPlanRepository($database);
        $container->set(YearPlanRepository::class, $yearPlanRepository);

        $planItemRepository = new PlanItemRepository($database);
        $container->set(PlanItemRepository::class, $planItemRepository);

        $yearPlanController = new YearPlanController($yearPlanRepository, $planItemRepository);
        $container->set(YearPlanController::class, $yearPlanController);
    }
}
