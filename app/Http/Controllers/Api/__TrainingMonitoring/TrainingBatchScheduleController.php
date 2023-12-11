<?php

namespace App\Http\Controllers\Api\TrainingMonitoring;

use App\Http\Controllers\Controller;
use App\Http\Requests\TrainingBatchScheduleRequest;
use App\Models\TrainingMonitoring\BatchScheduleDetail;
use App\Models\TrainingMonitoring\ClassAttendance;
use App\Models\TrainingMonitoring\TrainingBatchSchedule;
use App\Repositories\TrainingMonitoring\BatchScheduleDetailRepository;
use App\Repositories\TrainingMonitoring\ClassAttendanceRepository;
use App\Repositories\TrainingMonitoring\TrainingBatchScheduleRepository;
use App\Traits\TrainingMonitoring\UtilityTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TrainingBatchScheduleController extends Controller
{
    /*
     * Handle Bridge Between Database and Business layer
     */
    use UtilityTrait;
    private $trainingBatchScheduleRepository, $batchScheduleDetailRepository, $classAttendanceRepository;
    public function __construct(
        TrainingBatchScheduleRepository $trainingBatchScheduleRepository,
        ClassAttendanceRepository $classAttendanceRepository,
        BatchScheduleDetailRepository $batchScheduleDetailRepository,
    ) {
        $this->trainingBatchScheduleRepository = $trainingBatchScheduleRepository;
        $this->classAttendanceRepository = $classAttendanceRepository;
        $this->batchScheduleDetailRepository = $batchScheduleDetailRepository;
    }

    /*
     * Store Training Batch Schedule
     */
    public function store(TrainingBatchScheduleRequest $request)
    {
        try {
            $data = $request->all();
            $batch_schedule = $this->trainingBatchScheduleRepository->store($data);

            // make Batch Schedule Details
            $detail_data = [
                'batch_schedule_id' => $batch_schedule->id,
                'start_time' => $batch_schedule->class_time,
                'training_batch_id' => $batch_schedule->training_batch_id,
                'provider_id' => $batch_schedule->provider_id,
                'total_class' => $batch_schedule->class_duration,
                'class_days' => $batch_schedule->class_days,
            ];

            $this->batchScheduleDetailRepository->store($detail_data);

            return response()->json([
                'success' => true,
                'error' => false,
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => true,
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * show schedule/get schedule data
     */
    public function myClass($classScheduleId)
    {
        try {
            $classSchedule = TrainingBatchSchedule::find($classScheduleId);
            $classScheduleDetails = BatchScheduleDetail::where('batch_schedule_id', $classScheduleId)
                ->where('date', today())
                ->first();

            $user = auth()->user();
            $userType = $this->authUser($user->email);
            $user_id = $user->id;
            $user_role = $userType->role->name;
            $data = $classScheduleDetails;
            $classStatus = '';
            if ($classScheduleDetails) {
                $today = date('Y-m-d');

                $classStarted = $classScheduleDetails->isClassStarted();

                $classExpired = $classScheduleDetails->isClassExpired();

                if (!$classStarted) {
                    $classStatus = 0;
                } elseif ($classStarted && !$classExpired) {
                    $classStatus = 1;

                    $attendance = array();
                    $attendance['batch_schedule_detail_id'] = $classScheduleDetails->id;
                    $attendance['ProfileId'] = 1;
                    $attendance['is_present'] = 1;
                    $attendance['joining_time'] = Carbon::now()->format('H:i:s');

                    $myAttendant = $this->classAttendanceRepository->checkAttendants($classScheduleDetails->id, 1);

                    if (!$myAttendant) {
                        $myAttendant = $this->classAttendanceRepository->store($attendance);
                    }
                } elseif ($classExpired) {
                    $classStatus = -1;
                }
            }

            return response()->json([
                'success' => true,
                'error' => false,
                'data' => $classScheduleDetails,
                'classStatus' => $classStatus,
                'usrRole' => $user_role,
                'message' => 'Your attendance counted automatically',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => true,
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * check attendance of each batch
     */
    public function checkAttendance($ScheduleId)
    {
        try {
            $classSchedule = TrainingBatchSchedule::with('trainingBatch')
                ->where('id', $ScheduleId)->first();
            $user = auth()->user();
            $userType = $this->authUser($user->email);
            $user_id = $user->id;
            $user_role = $userType->role->name;
            $data['schedule'] = $classSchedule;
            $attendance = ClassAttendance::with('user')
                ->where('training_batch_schedule_id', $ScheduleId)
                ->where('attendant_date', today())
                ->where('is_present', 1)
                ->whereNotNull('trainee_id')
                ->get();
            $data['attendance'] = $attendance;
            $classStatus = '';
            if ($classSchedule) {
            }

            return response()->json([
                'success' => true,
                'error' => false,
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => true,
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * counter attendance mark as absent
     */
    public function counterAttendance(Request $request, $ScheduleId)
    {
        try {
            $selected_attendances = $request->input('selectedAttendIds');

            $result = $this->classAttendanceRepository->markAsAbsent($selected_attendances, $ScheduleId);

            return response([
                'success' => true,
                'error' => false,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => true,
                'message' => $e->getMessage(),
            ]);
        }
    }
}
