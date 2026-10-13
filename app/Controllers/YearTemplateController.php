<?php

namespace App\Controllers;

use App\Models\YearPlan;
use App\Repositories\PlanItemTemplateRepository;
use App\Repositories\YearTemplateRepository;
use App\Services\YearPlanCalendarService;
use Framework\Controller;
use Framework\Request;

class YearTemplateController extends Controller
{
    private YearPlanCalendarService $calendarService;

    public function __construct(
        private YearTemplateRepository $yearTemplateRepository,
        private PlanItemTemplateRepository $planItemTemplateRepository,
    ) {
        $this->calendarService = new YearPlanCalendarService();
    }

    public function index() {
        $yearTemplates = $this->yearTemplateRepository->all();
        return $this->responseFactory->view('year-templates/index.html.twig', compact('yearTemplates'));
    }

    public function show(Request $request)
    {
        $yearPlan  = $this->yearTemplateRepository->findById($request->get('id'));
        $planItems = $this->planItemTemplateRepository->findByYearPlanId($request->get('id'));

        $weeks = $this->calendarService->buildWeeks(
            $yearPlan->start_date,
            $yearPlan->end_date,
            $planItems
        );

        return $this->responseFactory->view('year-templates/show.html.twig', [
            'yearPlan' => $yearPlan,
            'weeks'    => $weeks,
        ]);
    }


    private function weeks(): array
    {
        $yearPlan = $this->yearPlanRepository->findById(1);
        $planItems = $this->planItemRepository->findByYearPlanId(1);

        return $this->calendarService->buildWeeks(
            $yearPlan->start_date,
            $yearPlan->end_date,
            $planItems
        );
    }
}