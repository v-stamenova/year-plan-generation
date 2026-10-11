<?php

namespace App\Controllers;

use App\Repositories\YearPlanRepository;
use Framework\Controller;

class YearPlanController extends Controller
{
    private YearPlanRepository $yearPlanRepository;

    public function __construct(YearPlanRepository $yearPlanRepository) {
        $this->yearPlanRepository = $yearPlanRepository;
    }

    public function index() {
        $yearPlan = $this->yearPlanRepository->findById(1);
        return $this->responseFactory->view('index.html.twig', ['yearPlan' => $yearPlan]);
    }
}