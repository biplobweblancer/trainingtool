<?php

namespace App\Http\Controllers\Api\TrainingMonitoring;

use App\Http\Controllers\Controller;
use App\Repositories\TrainingMonitoring\Interfaces\TraineeEnrollRepositoryInterface;
use App\Models\TrainingMonitoring\TrainingApplicant;
use Exception;

class TraineeEnrollController extends Controller
{
    /*
     * Handle Bridge Between Database and Business layer
     */
    private $traineeEnrollRepository;
    public function __construct(TraineeEnrollRepositoryInterface $traineeEnrollRepository)
    {
        
        $this->traineeEnrollRepository = $traineeEnrollRepository;
    }
    //
    public function index()
    {
        try {
            $traineeEnroll = $this->traineeEnrollRepository->all();
            return response()->json([
                'success' => true,
                'data' => $traineeEnroll,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle Course Provider details
     * 
     * @param Provider $provider
     * 
     * @return Json Response
     */
    public function show(TrainingApplicant $trainingApplicant)
    {
        try {
            $traineeEnroll = $this->traineeEnrollRepository->details($trainingApplicant->id);
            return response()->json([
                'success' => true,
                'data' => $traineeEnroll,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }
}
