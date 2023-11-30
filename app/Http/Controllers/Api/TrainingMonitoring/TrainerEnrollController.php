<?php

namespace App\Http\Controllers\Api\TrainingMonitoring;

use App\Http\Controllers\Controller;
use App\Repositories\TrainingMonitoring\Interfaces\TrainerEnrollRepositoryInterface;
use App\Models\TrainingMonitoring\ProvidersTrainer;
use Exception;

class TrainerEnrollController extends Controller
{
    /*
     * Handle Bridge Between Database and Business layer
     */
    private $trainerEnrollRepository;
    public function __construct(TrainerEnrollRepositoryInterface $trainerEnrollRepository)
    {
        $this->trainerEnrollRepository = $trainerEnrollRepository;
    }
    //
    public function index()
    {
        try {
            $trainerEnroll = $this->trainerEnrollRepository->all();
            return response()->json([
                'success' => true,
                'data' => $trainerEnroll,
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
    public function show(ProvidersTrainer $tmsProvidersTrainer)
    {
        try {
            $trainerEnroll = $this->trainerEnrollRepository->details($tmsProvidersTrainer->id);
            return response()->json([
                'success' => true,
                'data' => $trainerEnroll,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }
}
