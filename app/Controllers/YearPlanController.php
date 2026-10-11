<?php

namespace App\Controllers;

use App\Repositories\PlanItemRepository;
use App\Repositories\YearPlanRepository;
use Framework\Controller;

class YearPlanController extends Controller
{
    private YearPlanRepository $yearPlanRepository;
    private PlanItemRepository $planItemRepository;

    public function __construct(
        YearPlanRepository $yearPlanRepository,
        PlanItemRepository $planItemRepository
    ) {
        $this->yearPlanRepository = $yearPlanRepository;
        $this->planItemRepository = $planItemRepository;
    }

    public function index()
    {
        $yearPlan = $this->yearPlanRepository->findById(1);
        $planItems = $this->planItemRepository->findByYearPlanId(1);

        $weeks = $this->composeWeeks(
            $yearPlan->start_date->format('Y-m-d'),
            $yearPlan->end_date->format('Y-m-d'),
            $planItems
        );

        return $this->responseFactory->view('index.html.twig', [
            'yearPlan' => $yearPlan,
            'weeks' => $weeks,
        ]);
    }

    private function composeWeeks(
        string $startDate,
        string $endDate,
        array $planItems
    ): array {
        $start = new \DateTimeImmutable($startDate);
        $end = new \DateTimeImmutable($endDate);

        // Move to the Sunday on or before the start date (as per the HZ plan)
        $calendarStart = $start->modify('sunday this week');
        if ($start->format('w') === '0') {
            $calendarStart = $start;
        }

        // Move to the Saturday on or after the end date (as per the HZ plan)
        $calendarEnd = $end->modify('saturday this week');
        if ($end->format('w') === '6') {
            $calendarEnd = $end;
        }

        // Group plan items by their start date.
        $itemsByDate = [];
        foreach ($planItems as $planItem) {
            $date = $planItem->start_date->format('d-m-Y');
            $itemsByDate[$date][] = $planItem;
        }

        // Group based on weeks for easier rendering
        $weeks = [];

        for (
            $weekStart = $calendarStart;
            $weekStart <= $calendarEnd;
            $weekStart = $weekStart->modify('+1 week')
        ) {
            $weekNumber = (int) $weekStart->modify('+1 day')->format('W');

            $week = [];

            for ($day = 0; $day < 7; $day++) {
                $date = $weekStart->modify("+$day days");
                $dateKey = $date->format('d-m-Y');

                $week[$dateKey] = $itemsByDate[$dateKey] ?? [];
            }

            $weeks[$weekNumber] = $week;
        }

        return $weeks;
    }
}