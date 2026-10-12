<?php

namespace App\Controllers;

use App\Models\PlanItem;
use App\Repositories\CourseRepository;
use App\Repositories\PlanItemRepository;
use App\Repositories\YearPlanRepository;
use Framework\Controller;
use Framework\Request;

class YearPlanController extends Controller
{
    private YearPlanRepository $yearPlanRepository;
    private PlanItemRepository $planItemRepository;
    private CourseRepository $courseRepository;

    public function __construct(
        YearPlanRepository $yearPlanRepository,
        PlanItemRepository $planItemRepository,
        CourseRepository $courseRepository
    ) {
        $this->yearPlanRepository = $yearPlanRepository;
        $this->planItemRepository = $planItemRepository;
        $this->courseRepository = $courseRepository;
    }

    public function index()
    {
        // hardcoded for now
        $yearPlan = $this->yearPlanRepository->findById(1);
        $planItems = $this->planItemRepository->findByYearPlanId(1);
        $courses = $this->courseRepository->all();

        $weeks = $this->composeWeeks(
            $yearPlan->start_date->format('Y-m-d'),
            $yearPlan->end_date->format('Y-m-d'),
            $planItems
        );

        return $this->responseFactory->view('index.html.twig', [
            'yearPlan' => $yearPlan,
            'weeks' => $weeks,
            'courses' => $courses,
        ]);
    }

    public function update(Request $request) {
        $planItem = new PlanItem();
        $planItem->start_date = new \DateTime($request->get('start_date'));
        $planItem->year_plan_id = 1; //Hardcoded

        if (boolval($request->get('is_one_time'))) {
            $planItem->name = $request->get('name');
            $planItem->colour = $request->get('colour');

            if ($request->get('end_date')) {
                $planItem->end_date = new \DateTime($request->get('end_date'));
            }
        } else {
            $planItem->course_id = $request->get('course_id');
        }

        $this->planItemRepository->create($planItem);

        return $this->responseFactory->redirect('/');
    }

    private function composeWeeks(
        string $startDate,
        string $endDate,
        array $planItems
    ): array {
        $weeks = $this->createWeeks($startDate, $endDate);

        $this->addSingleDayItems($weeks, $planItems);
        $this->addMultiDayItems($weeks, $planItems);

        return $weeks;
    }

    /**
     * Create the calendar weeks and their seven empty days.
     */
    private function createWeeks(
        string $startDate,
        string $endDate
    ): array {
        $start = new \DateTimeImmutable($startDate);
        $end = new \DateTimeImmutable($endDate);

        $calendarStart = $start->modify('sunday this week');
        $calendarEnd = $end->modify('saturday this week');

        $weeks = [];

        for (
            $weekStart = $calendarStart;
            $weekStart <= $calendarEnd;
            $weekStart = $weekStart->modify('+1 week')
        ) {
            // Use Monday to determine the ISO week number.
            $weekNumber = (int) $weekStart
                ->modify('+1 day')
                ->format('W');

            $days = [];

            for ($day = 0; $day < 7; $day++) {
                $date = $weekStart->modify("+{$day} days");
                $dateKey = $date->format('d-m-Y');

                $days[$dateKey] = [];
            }

            $weeks[$weekNumber] = [
                'startDate' => $weekStart,
                'days' => $days,
                'multiDayItems' => [],
            ];
        }

        return $weeks;
    }

    /**
     * Assign single-day items to their corresponding dates.
     */
    private function addSingleDayItems(
        array &$weeks,
        array $planItems
    ): void {
        foreach ($planItems as $planItem) {
            if ($planItem->end_date !== null) {
                continue;
            }

            $dateKey = $planItem->start_date->format('d-m-Y');

            foreach ($weeks as &$week) {
                if (!array_key_exists($dateKey, $week['days'])) {
                    continue;
                }

                $week['days'][$dateKey][] = $planItem;
                break;
            }

            unset($week);
        }
    }

    /**
     * Split multi-day items across the weeks they overlap.
     */
    private function addMultiDayItems(
        array &$weeks,
        array $planItems
    ): void {
        foreach ($planItems as $planItem) {
            // Items without an end date are handled separately.
            if ($planItem->end_date === null) {
                continue;
            }

            $itemStart = $planItem->start_date;
            $itemEnd = $planItem->end_date;

            foreach ($weeks as &$week) {
                $weekStart = $week['startDate'];
                $weekEnd = $weekStart->modify('+6 days');

                // Skip weeks that do not overlap the event.
                if ($itemStart > $weekEnd || $itemEnd < $weekStart) {
                    continue;
                }

                $week['multiDayItems'][] = $this->createMultiDaySegment(
                    $planItem,
                    $weekStart,
                    $weekEnd
                );
            }

            unset($week);
        }
    }

    /**
     * Calculate the visible columns for an item within a single week.
     */
    private function createMultiDaySegment(
        object $planItem,
        \DateTimeImmutable $weekStart,
        \DateTimeImmutable $weekEnd
    ): array {
        $itemStart = $planItem->start_date;
        $itemEnd = $planItem->end_date;

        // Clip the event to the boundaries of this week.
        $visibleStart = $itemStart > $weekStart
            ? $itemStart
            : $weekStart;

        $visibleEnd = $itemEnd < $weekEnd
            ? $itemEnd
            : $weekEnd;

        // Sunday = column 1, Saturday = column 7.
        $startColumn = (int) $weekStart
                ->diff($visibleStart)
                ->days + 1;

        $span = (int) $visibleStart
                ->diff($visibleEnd)
                ->days + 1;

        return [
            'item' => $planItem,
            'startColumn' => $startColumn,
            'span' => $span,
        ];
    }
}