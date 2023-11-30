<?php

namespace App\Http\Controllers\Api\TrainingMonitoring;

use Carbon\Carbon;

use App\Http\Controllers\Controller;
use App\Models\TrainingMonitoring\Batch;
use App\Models\TrainingMonitoring\BatchScheduleDetail;
use App\Models\TrainingMonitoring\ClassAttendance;
use App\Models\TrainingMonitoring\District;
use App\Models\TrainingMonitoring\Division;
use App\Models\TrainingMonitoring\Provider;
use App\Models\TrainingMonitoring\ProvidersTrainer;
use App\Models\TrainingMonitoring\TrainerProfile;
use App\Models\TrainingMonitoring\Training;
use App\Models\TrainingMonitoring\TrainingApplicant;
use App\Models\TrainingMonitoring\TrainingBatch;
use App\Models\TrainingMonitoring\TrainingTitle;
use App\Models\TrainingMonitoring\Upazila;
use App\Models\TrainingMonitoring\User;
use App\Models\TrainingMonitoring\UserType;
use App\Models\TrainingMonitoring\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Traits\TrainingMonitoring\UtilityTrait;
use Exception;

class DashboardController extends Controller
{
    use UtilityTrait;
    public function summery()
    {
        // startDate < DATE_ADD (CURDATE(), INTERVAL duration DAY);
        try {
            $user = auth()->user();
            $userType = $this->authUser($user->email);


            $provider_id = $userType->provider_id;

            $totalDisision = Division::count();
            $totalDistrict = District::count();
            $totalUpazila = Upazila::count();

            if ($provider_id) {


                $totalBatchData = TrainingBatch::where('provider_id', $provider_id)->whereNotNull('startDate')->get();


                $runningBatch = TrainingBatch::where('provider_id', $provider_id)->whereNotNull('startDate')
                    ->whereRaw('date(startDate) <=  CURDATE()')
                    ->whereRaw('DATE_ADD(date(startDate), INTERVAL duration DAY) >=  CURDATE()')
                    ->count();
                $totalBatch = count($totalBatchData);
                $totalTrainer = ProvidersTrainer::where('provider_id', $provider_id)->count();
                $totalStudent = 0;
                if (count($totalBatchData) > 0) {

                    foreach ($totalBatchData as $trainee) {

                        $student = TrainingApplicant::where('BatchId', $trainee->id)->where('IsTrainee', 1)->first();
                        if ($student) {
                            $totalStudent = $totalStudent + 1;
                        }
                    }
                }
            } else {
                $totalBatch = TrainingBatch::count();
                $runningBatch = TrainingBatch::whereNotNull('startDate')
                    ->whereRaw('date(startDate) <=  CURDATE()')
                    ->whereRaw('DATE_ADD(date(startDate), INTERVAL duration DAY) >=  CURDATE()')
                    ->count();
                $totalTrainer = TrainerProfile::count();
                $totalStudent = TrainingApplicant::where('IsTrainee', 1)
                    ->count();

            }

            $totalProvider = Provider::count();
            $totalCourse = Training::count();
            //$totalProdiver = Provider::count();

            $totalCoordinator = 0;


            $totalPresentToday = BatchScheduleDetail::whereRaw('date=CURDATE()')->get();
            $totalPresent = 0;
            if (count($totalPresentToday) > 0) {
                foreach ($totalPresentToday as $row) {
                    $present = ClassAttendance::where('batch_schedule_detail_id', $row['id'])->where('is_present', 1)->get();
                    if ($present) {
                        $totalPresent = count($present) + $totalPresent;
                    }
                }
            }
            $todaysTotalPresent = $totalPresent;
            return response()->json([
                'success' => true,
                'data' => [
                    'totalDisision' => $totalDisision,
                    'totalDistrict' => 44,
                    'totalUpazila' => 130,
                    'totalBatch' => $totalBatch,
                    'totalStudent' => $totalStudent,
                    'totalProvider' => $totalProvider,
                    'totalCourse' => $totalCourse,
                    'runningBatch' => $runningBatch,
                    'completeBatch' => 0,
                    'totalTrainer' => $totalTrainer,
                    'totalCoordinator' => $totalCoordinator,
                    'totalPresentToday' => $todaysTotalPresent,
                ],
            ]);
        } catch (JWTException $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function courses()
    {
        $courses = TrainingTitle::all();

        return response()->json([
            'success' => true,
            'data' => $courses,
        ]);
    }

    public function batches(Request $request)
    {
        $batches = TrainingBatch::with(['Provider', 'getTraining.title'])
            ->whereHas('getTraining.title', function ($query) use ($request) {
                if ($request->course_id != '') {
                    $query->where('id', $request->course_id);
                }
            })
            ->when($request->batch != '', function ($query) use ($request) {
                $query->where('batchCode', 'like', '%' . $request->batch . '%');
            })
            ->whereHas('trainingApplicant.getProfile', function ($query) use ($request) {
                if ($request->district_id != '') {
                    $query->where('district_code', $request->district_id);
                }
                if ($request->division_id != '') {
                    $query->where('division_code', $request->division_id);
                }
            })
            ->take(20)->get();

        return response()->json([
            'success' => true,
            'data' => $batches,
        ]);
    }

    public function getAllbatches(Request $request)
    {

        $perPage = $request->input('per_page', 10);
        $batchStatus = $request->input('batch_status', '');
        $search = $request->input('search', '');

        $query = TrainingBatch::with('trainingBatchSchedule');

        if ($search) {
            $query->where('batchCode', 'like', "%$search%")
                ->orWhereHas('trainingBatchSchedule', function ($q) use ($batchStatus) {

                    if ($batchStatus) {
                        $q->whereNotNull('id');
                    } else {
                        $q->whereNotNull('id');
                    }
                });
        }


        $items = $query->paginate($perPage);

        return response()->json(['items' => $items]);
    }

    public function getAllProviders(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $status = $request->input('status', '');
        $search = $request->input('search', '');

        $query = Provider::query();

        if ($search) {
            $query->where('name', 'like', "%$search%");
        }


        $items = $query->paginate($perPage);

        return response()->json(['items' => $items]);
    }


    public function getAlldistricts(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search', '');
        $query = District::query();

        if ($search) {
            $query->where('Name', 'like', "%$search%")
                ->orWhere('NameEng', 'like', "%$search%")
                ->orWhere('Code', 'like', "%$search%");
        }
        $items = $query->paginate($perPage);
        return response()->json(['items' => $items]);
    }
    public function getAllupazilas(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search', '');
        $query = Upazila::query();

        if ($search) {
            $query->where('Name', 'like', "%$search%")
                ->orWhere('NameEng', 'like', "%$search%")
                ->orWhere('Code', 'like', "%$search%");
        }
        $items = $query->paginate($perPage);
        return response()->json(['items' => $items]);

    }
    public function getAllpartners(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search', '');
        $query = Provider::query();

        if ($search) {
            $query->where('Name', 'like', "%$search%")
                ->orWhere('NameEng', 'like', "%$search%")
                ->orWhere('Code', 'like', "%$search%");
        }
        $items = $query->paginate($perPage);
        return response()->json(['items' => $items]);
    }
    public function getAlltrainers(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 50);
            $search = $request->input('search', '');
            $dateFilter = $request->input('date_filter', '');

            $items = TrainerProfile::with('profile')
                ->whereHas('profile', function ($query) use ($search) {
                    if ($search) {
                        $query->where('KnownAs', 'like', "%$search%")
                            ->orWhere('NID', 'like', "%$search%")
                            ->orWhere('Phone', 'like', "%$search%");
                    }
                })
                ->orWhere(function ($query) use ($search) {
                    if ($search) {
                        $query->where('professionalBio', 'like', "%$search%");
                    }
                })
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'items' => $items
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error processing the request: ' . $e->getMessage()
            ], 500);
        }

    }
    public function getAlltrainees(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 10);
            $status = $request->input('status', '');
            $search = $request->input('search', '');

            $query = TrainerProfile::query();

            if ($search) {
                $query->where('name', 'like', "%$search%");
            }

            $items = $query->paginate($perPage);

            return response()->json(['items' => $items]);
        } catch (\Exception $e) {
            // Log the exception or handle it as needed
            return response()->json(['error' => 'An error occurred.']);
        }

    }

    public function dashboardTotalsuoeradmin(Request $request)
    {

        try {
            $district = $request->input('district', '');
            $upazila = $request->input('upazila', '');
            $division = $request->input('division', '');
            $usertype = $request->input('usertype', '');
            $profileId = $request->input('profileId', '');

            $filter = '';

            if ($division) {
                $filter .= "$division";
            }

            if ($district) {
                $filter .= "-$district";
            }

            if ($upazila) {
                $filter .= "-$upazila";
            }

            // dd($filter);
            if ($usertype == "superadmin") {
                $trainingBatchall = TrainingBatch::all();
                $filteredBatch = TrainingBatch::where('GEOCode', 'like', "%$filter%");
                $runingBatch = $filteredBatch->whereNotNull('startDate')
                    ->whereRaw('date(startDate) <=  CURDATE()')
                    ->whereRaw('DATE_ADD(date(startDate), INTERVAL duration DAY) >=  CURDATE()')
                    ->count();
                $batchCount = $filteredBatch->count();
                $districtCount = District::where('Code', '=', $district)->count();
                $upazilaCount = Upazila::where('Code', '=', $upazila)->count();
                $divisionCount = Division::where('Code', '=', $division)->count();
                $divisionCount = Division::where('Code', '=', $division)->count();

                $data = [
                    "provider" => Provider::count(),
                    "applicant" => TrainingApplicant::where('IsTrainee', 1)->count(),
                    "filterBatch" => $batchCount,
                    "runingBatch" => $runingBatch,
                    'completeBatch' => 0,
                    "district" => $districtCount ?: District::count(),
                    "upazila" => $upazilaCount ?: Upazila::count(),
                    "division" => $divisionCount ?: Division::count(),
                    "totalBatchs" => $trainingBatchall->count(),
                    "totaltrainer" => TrainerProfile::count(),
                ];
                # code...
            } elseif ($usertype == "Admin") {
                // division 
                $userType = UserType::where('profileId', $profileId)->first();
                if ($userType) {
                    $districtId = $userType->district->Code;
                    // dd($districtId);

                    $trainingBatchall = TrainingBatch::all();
                    $filteredBatch = TrainingBatch::where('GEOCode', 'like', "%$districtId%");
                    $batchCount = $filteredBatch->count();

                    $runingBatch = $filteredBatch->whereNotNull('startDate')
                        ->whereRaw('date(startDate) <=  CURDATE()')
                        ->whereRaw('DATE_ADD(date(startDate), INTERVAL duration DAY) >=  CURDATE()')
                        ->count();
                    $month = 11;
                    $year = 2023;
                    $firstDayOfMonth = Carbon::create($year, $month, 1)->startOfDay();
                    $lastDayOfMonth = $firstDayOfMonth->copy()->endOfMonth();
                    // dd($filteredBatch);
                    $monthlyAttendance = ClassAttendance::whereBetween('created_at', [$firstDayOfMonth, $lastDayOfMonth])
                        ->with('scheduleDetail')
                        ->get();

                    $attendanceSheet = [];
                    for ($currentDay = $firstDayOfMonth->copy(); $currentDay->lte($lastDayOfMonth); $currentDay->addDay()) {
                        $dailyAttendance = $monthlyAttendance->filter(function ($attendance) use ($currentDay) {
                            return $attendance->created_at && $attendance->created_at->isSameDay($currentDay);
                        });
                        $attendanceSheet[$currentDay->format('Y-m-d')] = [
                            'totalAttendance' => $dailyAttendance->count(),
                            'details' => $dailyAttendance->map(function ($attendance) {
                                return [
                                    'schedule_detail_id' => $attendance->scheduleDetail->id,
                                    'status' => $attendance->status,
                                ];
                            })->all(),
                        ];
                    }
                    $totalAttendanceSum = 0;

                    foreach ($attendanceSheet as $dayData) {
                        $totalAttendanceSum += $dayData['totalAttendance'];
                    }
                    $districtCount = District::where('Code', '=', $districtId)->count();
                    $upazilaCount = Upazila::where('Code', '=', $upazila)->count();
                    $divisionCount = Division::where('Code', '=', $division)->count();
                    $divisionCount = Division::where('Code', '=', $division)->count();

                    $data = [
                        "provider" => Provider::count(),
                        "applicant" => TrainingApplicant::where('IsTrainee', 1)
                            ->whereHas('profile', function ($query) use ($districtId) {
                                $query->where('district_code', $districtId);
                            })
                            ->count(),
                        "filterBatch" => $batchCount,
                        "runingBatch" => $runingBatch,
                        "district" => $districtCount,
                        "totalAttendancebymonth" => $totalAttendanceSum,
                        "upazila" => $upazilaCount ?: Upazila::count(),
                        "division" => $divisionCount ?: Division::count(),
                        "totalBatchs" => $trainingBatchall->count(),
                        "totaltrainer" => TrainerProfile::count(),
                    ];
                } else {
                    // Handle the case when no record is found
                    dd('UserType record not found for profileId: ' . $profileId);
                }


            } elseif ($usertype == "DPD") {
                // division 
                //$userType = UserType::where('profileId', $profileId)->first();
                $userType = Profile::where('id', $profileId)->first();
                if ($userType) {
                    $division_code = $userType->division_code;
                    $trainingBatchall = TrainingBatch::all();
                    $filteredBatch = TrainingBatch::where('GEOCode', 'like', "%$division_code%");
                    $batchCount = $filteredBatch->count();

                    $runingBatch = $filteredBatch->whereNotNull('startDate')
                        ->whereRaw('date(startDate) <=  CURDATE()')
                        ->whereRaw('DATE_ADD(date(startDate), INTERVAL duration DAY) >=  CURDATE()')
                        ->count();

                    $month = 11;
                    $year = 2023;
                    $firstDayOfMonth = Carbon::create($year, $month, 1)->startOfDay();
                    $lastDayOfMonth = $firstDayOfMonth->copy()->endOfMonth();
                    // dd($filteredBatch);
                    $monthlyAttendance = ClassAttendance::whereBetween('created_at', [$firstDayOfMonth, $lastDayOfMonth])
                        ->with('scheduleDetail')
                        ->get();

                    $attendanceSheet = [];
                    for ($currentDay = $firstDayOfMonth->copy(); $currentDay->lte($lastDayOfMonth); $currentDay->addDay()) {
                        $dailyAttendance = $monthlyAttendance->filter(function ($attendance) use ($currentDay) {
                            return $attendance->created_at && $attendance->created_at->isSameDay($currentDay);
                        });
                        $attendanceSheet[$currentDay->format('Y-m-d')] = [
                            'totalAttendance' => $dailyAttendance->count(),
                            'details' => $dailyAttendance->map(function ($attendance) {
                                return [
                                    'schedule_detail_id' => $attendance->scheduleDetail->id,
                                    'status' => $attendance->status,
                                ];
                            })->all(),
                        ];
                    }
                    $totalAttendanceSum = 0;

                    foreach ($attendanceSheet as $dayData) {
                        $totalAttendanceSum += $dayData['totalAttendance'];
                    }





                    $currentDateTime = Carbon::now();

                    $runningSchedules = BatchScheduleDetail::where('start_time', '<=', $currentDateTime)
                        ->where('end_time', '>=', $currentDateTime)
                        ->get();

                    $runningBatchIds = $runningSchedules->pluck('batch_id')->unique();

                    $runningBatches = Batch::whereIn('id', $runningBatchIds)->get();

                    $numberOfRunningBatches = $runningBatches->count();

                    dd($numberOfRunningBatches);

                    $districtCount = District::where('ParentCode', '=', $division_code)->count();
                    $upazilaCount = Upazila::where('Code', '=', $upazila)->count();
                    $divisionCount = Division::where('Code', '=', $division)->count();

                    $data = [
                        "provider" => Provider::count(),
                        "applicant" => TrainingApplicant::where('IsTrainee', 1)
                            ->whereHas('profile', function ($query) use ($division_code) {
                                $query->where('division_code', $division_code);
                            })
                            ->count(),
                        "filterBatch" => $batchCount,
                        "totalAttendancebymonth" => $totalAttendanceSum,
                        "runingBatch" => $runingBatch,
                        "district" => $districtCount ?: District::count(),
                        "upazila" => $upazilaCount ?: Upazila::count(),
                        "division" => $divisionCount ?: Division::count(),
                        "totalBatchs" => $trainingBatchall->count(),
                        "totaltrainer" => TrainerProfile::count(),
                    ];
                } else {
                    // Handle the case when no record is found
                    dd('UserType record not found for profileId: ' . $profileId);
                }


            } elseif ($usertype == "DC") {
                ///

            } elseif ($usertype == "UNO") {

            } elseif ($usertype == "PIUOfficer") {

            } elseif ($usertype == "Member") {

            } elseif ($usertype == "Trainee") {

            } elseif ($usertype == "Trainer") {


            } elseif ($usertype == "Inspector") {


            } elseif ($usertype == "Provider") {

                // division 
                $userType = UserType::where('profileId', $profileId)->first();
                if ($userType) {
                    $districtId = $userType->district->Code;
                    // dd($districtId);

                    $trainingBatchall = TrainingBatch::all();
                    $filteredBatch = TrainingBatch::where('GEOCode', 'like', "%$districtId%");
                    $batchCount = $filteredBatch->count();

                    $runingBatch = $filteredBatch->whereNotNull('startDate')
                        ->whereRaw('date(startDate) <=  CURDATE()')
                        ->whereRaw('DATE_ADD(date(startDate), INTERVAL duration DAY) >=  CURDATE()')
                        ->count();

                    $districtCount = District::where('Code', '=', $districtId)->count();
                    $upazilaCount = Upazila::where('Code', '=', $upazila)->count();
                    $divisionCount = Division::where('Code', '=', $division)->count();
                    $divisionCount = Division::where('Code', '=', $division)->count();

                    $data = [
                        "provider" => Provider::count(),
                        "applicant" => TrainingApplicant::where('IsTrainee', 1)
                            ->whereHas('profile', function ($query) use ($districtId) {
                                $query->where('district_code', $districtId);
                            })
                            ->count(),
                        "filterBatch" => $batchCount,
                        "runingBatch" => $runingBatch,
                        "district" => $districtCount ?: District::count(),
                        "upazila" => $upazilaCount ?: Upazila::count(),
                        "division" => $divisionCount ?: Division::count(),
                        "totalBatchs" => $trainingBatchall->count(),
                        "totaltrainer" => TrainerProfile::count(),
                    ];
                } else {
                    // Handle the case when no record is found
                    dd('UserType record not found for profileId: ' . $profileId);
                }


            } elseif ($usertype == "Coordinator") {


            } else {
                # code...
            }


            return response()->json(['items' => $data]);
        } catch (\Exception $e) {
            // Handle the exception, log it, or return an error response
            return response()->json(['error' => 'An error occurred while processing the request.']);
        }

    }
}
