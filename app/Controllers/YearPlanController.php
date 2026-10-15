<?php

namespace App\Controllers;

use App\DTOs\CalendarWeek;
use App\Models\PlanItem;
use App\Repositories\CourseRepository;
use App\Repositories\PlanItemRepository;
use App\Repositories\YearPlanRepository;
use App\Services\YearPlanCalendarService;
use Framework\Controller;
use Framework\Request;

class YearPlanController extends Controller
{
    private YearPlanRepository $yearPlanRepository;
    private PlanItemRepository $planItemRepository;
    private CourseRepository $courseRepository;
    private YearPlanCalendarService $calendarService;

    public function __construct(
        YearPlanRepository $yearPlanRepository,
        PlanItemRepository $planItemRepository,
        CourseRepository $courseRepository
    ) {
        $this->yearPlanRepository = $yearPlanRepository;
        $this->planItemRepository = $planItemRepository;
        $this->courseRepository = $courseRepository;
        $this->calendarService = new YearPlanCalendarService();
    }

    public function index() {
        $yearTemplates = $this->yearPlanRepository->all();
        return $this->responseFactory->view('year-plans/index.html.twig', compact('yearTemplates'));
    }

    public function show(Request $request)
    {
        // hardcoded for now
        $yearPlan  = $this->yearPlanRepository->findById($request->get('id'));
        $planItems = $this->planItemRepository->findByYearPlanId($request->get('id'));
        $courses   = $this->courseRepository->all();

        $weeks = $this->calendarService->buildWeeks(
            $yearPlan->start_date,
            $yearPlan->end_date,
            $planItems
        );

        return $this->responseFactory->view('year-plans/show.html.twig', [
            'yearPlan' => $yearPlan,
            'weeks'    => $weeks,
            'courses'  => $courses,
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

    public function print(Request $request)
    {
        $yearPlan = $this->yearPlanRepository->findById(1);

        $from = new \DateTimeImmutable($request->get('from') ?: $yearPlan->start_date->format('Y-m-d'));
        $to = new \DateTimeImmutable($request->get('to') ?: $yearPlan->end_date->format('Y-m-d'));

        $weeks = array_filter(
            $this->weeks(),
            fn (CalendarWeek $week) => $week->endDate() >= $from && $week->startDate <= $to
        );

        return $this->responseFactory->view('print.html.twig', [
            'weeks' => $weeks,
            'from'  => $from,
            'to'    => $to,
        ]);
    }

    public function delete(Request $request) {
        $item = $this->planItemRepository->findById($request->get('item_id'));
        if ($item) {
            $this->planItemRepository->delete($item);
        }

        return $this->responseFactory->view('components/_calendar.html.twig', [
            'weeks' => $this->weeks(),
        ]);
    }

    public function update(Request $request) {
        $planItem = new PlanItem();
        $planItem->start_date = new \DateTime($request->get('start_date'));
        $planItem->year_plan_id = $request->get('id');

        if (boolval($request->get('is_one_time'))) {
            $planItem->name = $request->get('name');
            $planItem->colour = $request->get('colour');
            $planItem->is_main = (bool)$request->get('allDay');

            if ($request->get('end_date')) {
                $planItem->end_date = new \DateTime($request->get('end_date'));
            }
        } else {
            $planItem->course_id = $request->get('course_id');
        }

        if ($request->get('repeat_until') && count($request->getMany('repeat_days')) > 0) {
            $dates = $this->calendarService->getRepeatDates($request->get('start_date'), $request->get('repeat_until'), $request->getMany('repeat_days'));

            foreach ($dates as $date) {
                $planItem->start_date = new \DateTime($date);
                $this->planItemRepository->create($planItem);
            }
        } else {
            $this->planItemRepository->create($planItem);
        }

        return $this->responseFactory->view('components/_calendar.html.twig', [
            'weeks' => $this->weeks(),
        ]);
    }
}