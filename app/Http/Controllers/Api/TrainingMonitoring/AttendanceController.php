<?php

namespace App\Http\Controllers\Api\TrainingMonitoring;

use App\Http\Controllers\Controller;
use App\Http\Requests\TrainingMonitoring\AttendanceTakeRequest;
use App\Http\Requests\TrainingMonitoring\ClassStartRequest;
use App\Models\TrainingMonitoring\BatchScheduleDetail;
use App\Models\TrainingMonitoring\ClassAttendance;
use App\Models\TrainingMonitoring\ProvidersTrainer;
use App\Models\TrainingMonitoring\TrainingBatch;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Traits\TrainingMonitoring\UtilityTrait;

class AttendanceController extends Controller
{
    use UtilityTrait;
    public function batchList()
    {
        try {
            $user = auth()->user();
            $userType = $this->authUser($user->email);
            $trainer = ProvidersTrainer::where('ProfileId', $userType->ProfileId)->first();
            if ($trainer == null) {
                return response()->json([
                    'success' => false,
                    'data' => [],
                ]);
            }
            $batches = TrainingBatch::with('getTraining.title', 'schedule')
                ->whereNotNull('startDate')
                ->where('provider_id', $trainer->provider_id)
                ->whereHas('providerTrainers', function ($query) use ($trainer) {
                    $query->where('ProfileId', $trainer->ProfileId);
                })
                ->whereHas('schedule')
                ->get();
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'error' => true,
                'message' => $th->getMessage(),
            ]);
        }
        return response()->json([
            'success' => true,
            'data' => $batches,
        ]);
    }

    public function start(ClassStartRequest  $request)
    {
        $validated_data = $request->validated();

        $schedule_detail = BatchScheduleDetail::where('id', $validated_data['schedule_detail_id'])
            ->where('status', 1)
            ->first();

        if ($schedule_detail == null) {
            return response()->json([
                'success' => false,
                'message' => 'Data not found',
            ]);
        }

        try {
            $trainees = [];
            foreach ($schedule_detail->schedule->trainingBatch->trainees as $trainee) {
                $trainees[] = [
                    'batch_schedule_detail_id' => $schedule_detail->id,
                    'ProfileId' => $trainee->ProfileId,
                    'is_present' => null,
                    'joining_time' => '00:00:00',
                ];
            }

            ClassAttendance::insert($trainees);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
            ]);
        }

        $schedule_detail->update([
            'start_time' => Carbon::now()->format('H:i:s'),
            'status' => 2,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Class started successfully',
        ]);
    }

    public function end(ClassStartRequest  $request)
    {
        $validated_data = $request->validated();

        $schedule_detail = BatchScheduleDetail::where('id', $validated_data['schedule_detail_id'])
            ->where('status', 2)
            ->first();

        if ($schedule_detail == null) {
            return response()->json([
                'success' => false,
                'message' => 'Data not found',
            ]);
        }

        $schedule_detail->update([
            'end_time' => Carbon::now()->format('H:i:s'),
            'status' => 3,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Class ended successfully',
        ]);
    }

    public function allSchedule($id)
    {
        try {
            $schedules = BatchScheduleDetail::where('batch_schedule_id', $id)
                ->get();
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'error' => true,
                'message' => $th->getMessage(),
            ]);
        }
        return response()->json([
            'success' => true,
            'data' => $schedules,
        ]);
    }

    public function studentList($id)
    {
        try {
            $trainees = ClassAttendance::with('profile', 'scheduleDetail')->where('batch_schedule_detail_id', $id)
                ->get();
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'error' => true,
                'message' => $th->getMessage(),
            ]);
        }
        return response()->json([
            'success' => true,
            'data' => $trainees,
        ]);
    }

    public function take(AttendanceTakeRequest $request)
    {
        try {
            $validated_data = $request->validated();
            $schedule_detail = BatchScheduleDetail::find($validated_data['batch_schedule_detail_id']);
            if ($schedule_detail->status != 2) {
                return response()->json([
                    'success' => false,
                    'message' => 'You can not update the attendance of this day',
                ]);
            }
            $attendances =  ClassAttendance::where('batch_schedule_detail_id', $validated_data['batch_schedule_detail_id'])
                ->get();

            foreach ($attendances as  $attendance) {
                if (in_array($attendance->ProfileId, $validated_data['trainees'])) {
                    $attendance->is_present = 1;
                } else {
                    $attendance->is_present = 0;
                }
                $attendance->save();
            }
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'error' => true,
                'message' => $th->getMessage(),
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => 'Attendance updated successfully',
        ]);
    }

    // public function all batches for office
    public function allBatches(Request $request)
    {
        try {
            $search_batch = $request['batch'] ?? '';
            // dd($request['page']);
            if ($search_batch !== '') {
                $batches = TrainingBatch::with(['getTraining.title', 'schedule'])
                    ->where('batchCode', 'LIKE', '%' . $search_batch . '%')
                    ->paginate(20);
                // dd($batches);
            } else {
                $batches = TrainingBatch::with('getTraining.title', 'schedule')
                    ->paginate(20);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'error' => true,
                'message' => $th->getMessage(),
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $batches,
        ]);
    }
}
