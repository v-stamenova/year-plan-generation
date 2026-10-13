<?php

namespace App\Controllers;

use App\Models\YearPlan;
use App\Repositories\YearTemplateRepository;
use Framework\Controller;

class YearTemplateController extends Controller
{
    public function __construct(
        private YearTemplateRepository $yearTemplateRepository,
    ) {}

    public function index() {
        $yearTemplates = $this->yearTemplateRepository->all();
        return $this->responseFactory->view('year-templates/index.html.twig', compact('yearTemplates'));
    }
}