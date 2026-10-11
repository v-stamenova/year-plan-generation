<?php

namespace App\Controllers;

use App\Models\PlanItem;
use App\Repositories\PlanItemRepository;
use App\Repositories\YearPlanRepository;
use Framework\Controller;

class YearPlanController extends Controller
{
    private YearPlanRepository $yearPlanRepository;
    private PlanItemRepository $planItemRepository;

    public function __construct(YearPlanRepository $yearPlanRepository, PlanItemRepository $planItemRepository) {
        $this->yearPlanRepository = $yearPlanRepository;
        $this->planItemRepository = $planItemRepository;
    }

    public function index() {
        $yearPlan = $this->yearPlanRepository->findById(1);
        $planItem = $this->planItemRepository->findById(1);
        $planItem2 = $this->planItemRepository->findById(2);
        $week = [
            "06-09-2026" => [],
            "07-09-2026" => [$planItem],
            "08-09-2026" => [],
            "09-09-2026" => [$planItem2],
            "10-09-2026" => [],
            "11-09-2026" => [],
            "12-09-2026" => [],
        ];

        return $this->responseFactory->view('index.html.twig',
            ['yearPlan' => $yearPlan
            , 'week' => $week]);
    }
}