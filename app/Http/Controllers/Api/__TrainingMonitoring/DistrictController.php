<?php

namespace App\Http\Controllers\Api\TrainingMonitoring;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\TrainingMonitoring\Interfaces\DistrictRepositoryInterface;
use App\Repositories\TrainingMonitoring\Interfaces\DivisionRepositoryInterface;
use App\Http\Requests\TrainingMonitoring\StoreDistrictRequest;
use App\Http\Requests\TrainingMonitoring\UpdateDistrictRequest;
use App\Models\TrainingMonitoring\District;
use Symfony\Component\HttpFoundation\Response;
use Exception;

class DistrictController extends Controller
{
    /*
     * Handle Bridge Between Database and Business layer
     */
    private $districtRepository;
    private $divisionRepository;
    public function __construct(DistrictRepositoryInterface $districtRepository, DivisionRepositoryInterface $divisionRepository)
    {
        $this->districtRepository = $districtRepository;
        $this->divisionRepository = $divisionRepository;
    }

    /**
     * Display all district
     *
     * @return Json Response
     */
    public function index($division_code = null)
    {

        try {
            if ($division_code) {
                $district = $this->districtRepository->all($division_code);
            } else {
                $district = $this->districtRepository->all();
            }

            return response()->json([
                'success' => true,
                'data' => $district,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }
    /**
     * Handle Course District details
     * @return Json Response
     */
    public function show(District $district)
    {
        try {
            $district = $this->districtRepository->details($district->id);
            return response()->json([
                'success' => true,
                'data' => $district,
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }
    /**
     * Handle Course District request
     *
     * @param StoreDistrictRequest $request
     *
     * @return Json Response
     */
    public function store(StoreDistrictRequest $request)
    {
        try {
            $data = $request->all();
            $districts = $this->districtRepository->store($data);
            return response()->json([
                'success' => true,
                'message' => 'District Created Successfully',
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle Course District Edit request
     *
     * @param District $district
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(District $district)
    {
        try {
            $divisions = $this->divisionRepository->all();
            $data = [
                'district' => $district,
                'divisions' => $divisions,
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
     * Update District data
     *
     * @param District $district
     * @param UpdateDistrictRequest $request
     *
     * @return json Response
     */
    public function update(District $district, UpdateDistrictRequest $request)
    {

        try {
            $data = $request->all();
            $this->districtRepository->update($district, $data);
            return response()->json([
                'success' => true,
                'data' => $district->name,
                'message' => 'District Updated Successfully',
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Delete District data
     *
     * @param District $district
     *
     * @return json Response
     */
    public function destroy(District $district)
    {
        try {
            $district->delete();
            return response()->json([
                'success' => true,
                'message' => 'District Deleted Successfully',
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }
}
