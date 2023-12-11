<?php

namespace App\Http\Controllers\Api\TrainingMonitoring;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\TrainingMonitoring\Interfaces\DistrictRepositoryInterface;
use App\Repositories\TrainingMonitoring\Interfaces\DivisionRepositoryInterface;
use App\Repositories\TrainingMonitoring\Interfaces\CommitteeRepositoryInterface;
use App\Repositories\TrainingMonitoring\Interfaces\AdminRepositoryInterface;
use App\Http\Requests\TrainingMonitoring\StoreCommitteeRequest;
use App\Http\Requests\TrainingMonitoring\UpdateCommitteeRequest;
use App\Models\TrainingMonitoring\Committee;
use Symfony\Component\HttpFoundation\Response;
use Exception;

class CommitteeController extends Controller
{
    /*
     * Handle Bridge Between Database and Business layer
     */
    private $districtRepository;
    private $divisionRepository;
    private $committeeRepository;
    private $adminRepository;
    public function __construct(AdminRepositoryInterface $adminRepository,CommitteeRepositoryInterface $committeeRepository, DistrictRepositoryInterface $districtRepository, DivisionRepositoryInterface $divisionRepository)
    {
        
        $this->districtRepository = $districtRepository;
        $this->divisionRepository = $divisionRepository;
        $this->committeeRepository = $committeeRepository;
        $this->adminRepository = $adminRepository;
    }

    /**
     * Display all committee
     *
     * @return Json Response
     */
    public function index()
    {
        try {
            $committee = $this->committeeRepository->all();
            $adminUser = $this->adminRepository->all();
            return response()->json([
                'success' => true,
                'data' => $committee,
                'committees' => $adminUser
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }
    /**
     * Handle Committee details
     * @return Json Response
     */
    public function show(Committee $committee)
    {
        try {
            $committeeDetails = $this->committeeRepository->details($committee->id);
            return response()->json([
                'success' => true,
                'data' => $committee,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }
    /**
     * Handle Committee request
     *
     * @param StoreCommitteeRequest $request
     *
     * @return Json Response
     */
    public function store(StoreCommitteeRequest $request)
    {
        try {
            $data = $request->all();
            $committees = $this->committeeRepository->store($data);
            return response()->json([
                'success' => true,
                'message' => 'Committee Created Successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

     /**
     * Handle Committee Edit request
     *
     * @param Committee $committee
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Committee $committee)
    {
        try {
            $district = $this->districtRepository->all();
            $data = [
                'district' => $district,
                'committees' => $committee,
            ];
            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Update Committee data
     *
     * @param Committee $committee
     * @param UpdateCommitteeRequest $request
     *
     * @return json Response
     */
    public function update(Committee $committee, UpdateCommitteeRequest $request)
    {

        try {
            $data = $request->all();
            $this->committeeRepository->update($committee, $data);
            return response()->json([
                'success' => true,
                'data' => $committee->name,
                'message' => 'Committee Updated Successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Delete Committee data
     *
     * @param Committee $committee
     *
     * @return json Response
     */
    public function destroy(Committee $committee)
    {
        try {
            $Committee->delete();
            return response()->json([
                'success' => true,
                'message' => 'Committee Deleted Successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }
}
