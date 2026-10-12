<?php

namespace App\Controllers;

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

    public function index()
    {
        // hardcoded for now
        $yearPlan  = $this->yearPlanRepository->findById(1);
        $planItems = $this->planItemRepository->findByYearPlanId(1);
        $courses   = $this->courseRepository->all();

        $weeks = $this->calendarService->buildWeeks(
            $yearPlan->start_date,
            $yearPlan->end_date,
            $planItems
        );

        return $this->responseFactory->view('index.html.twig', [
            'yearPlan' => $yearPlan,
            'weeks'    => $weeks,
            'courses'  => $courses,
        ]);
    }

    public function delete(Request $request) {
        $item = $this->planItemRepository->findById($request->get('item_id'));
        $this->planItemRepository->delete($item);

        return $this->responseFactory->redirect("/");
    }

    public function update(Request $request) {
        $planItem = new PlanItem();
        $planItem->start_date = new \DateTime($request->get('start_date'));
        $planItem->year_plan_id = 1; //Hardcoded

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
            $dates = $this->getRepeatDates($request->get('start_date'), $request->get('repeat_until'), $request->getMany('repeat_days'));

            foreach ($dates as $date) {
                $planItem->start_date = new \DateTime($date);
                $this->planItemRepository->create($planItem);
            }
        } else {
            $this->planItemRepository->create($planItem);
        }


        return $this->responseFactory->redirect('/');
    }

    private function getRepeatDates(
        string $startDate,
        string $repeatUntil,
        array $repeatDays
    ): array {
        $dates = [];

        $start = new \DateTimeImmutable($startDate);
        $end = new \DateTimeImmutable($repeatUntil);

        $selectedDays = array_map('intval', $repeatDays);

        for ($date = $start; $date <= $end; $date = $date->modify('+1 day')) {
            // ISO weekday: Monday = 1, Sunday = 7
            $dayOfWeek = (int) $date->format('N');

            if (in_array($dayOfWeek, $selectedDays, true)) {
                $dates[] = $date->format('Y-m-d');
            }
        }

        return $dates;
    }

}