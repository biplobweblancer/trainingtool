<?php

namespace App\Http\Controllers\Api\TrainingMonitoring;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TrainingMonitoring\Inspection;
use App\Traits\TrainingMonitoring\UtilityTrait;

class InspectionController extends Controller
{
    use UtilityTrait;
    public function index(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 500);
            $search = $request->input('search', '');
            $dateFilter = $request->input('date_filter', '');
            $query = Inspection::with('batch.training.trainingTitle', 'batch.provider', 'createdBy');

            if ($search) {
                $query->where(function ($innerQuery) use ($search) {
                    $innerQuery->whereHas('batch.training.trainingTitle', function ($titleQuery) use ($search) {
                        $titleQuery->where('Name', 'like', "%$search%")
                            ->orWhere('NameEn', 'like', "%$search%");
                    })
                        ->orWhereHas('createdBy', function ($createdByQuery) use ($search) {
                            $createdByQuery
                                ->where('KnownAs', 'like', "%$search%")
                                ->orWhere('KnownAsBangla', 'like', "%$search%")
                                ->orWhere('Phone', 'like', "%$search%");
                        })
                        ->orWhereHas('batch', function ($batchQuery) use ($search) {
                            $batchQuery->where('batchCode', 'like', "%$search%");
                        })
                        ->orWhereHas('batch.provider', function ($batchQuery) use ($search) {
                            $batchQuery->where('name', 'like', "%$search%");
                        });
                });
            }

            $inspections = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $inspections
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error processing the request: ' . $e->getMessage()
            ], 500); // You can customize the status code based on your requirements
        }
    }
    public function store(Request $request)
    {
        $request['created_by'] = 41;
        return Inspection::create($request->all());
    }

    public function show($id)
    {
        return Inspection::with('batch.training.trainingTitle', 'batch.primaryTrainer.profile', 'createdBy')->find($id);
    }

    public function update(Request $request, $id)
    {
        $inspection = Inspection::findOrFail($id);
        $inspection->update($request->all());
        return $inspection;
    }

    public function destroy($id)
    {
        Inspection::find($id)->delete();
        return 204;
    }

}