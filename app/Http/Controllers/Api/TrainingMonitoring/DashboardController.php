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
use App\Models\TrainingMonitoring\TrainingBatchSchedule;
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
        // try {
        //     $user = auth()->user();
        //     $userType = $this->authUser($user->email);


        //     $provider_id = $userType->provider_id;

        //     $totalDisision = Division::count();
        //     $totalDistrict = District::count();
        //     $totalUpazila = Upazila::count();

        //     if ($provider_id) {


        //         $totalBatchData = TrainingBatch::where('provider_id', $provider_id)->whereNotNull('startDate')->get();


        //         $runningBatch = TrainingBatch::where('provider_id', $provider_id)->whereNotNull('startDate')
        //             ->whereRaw('date(startDate) <=  CURDATE()')
        //             ->whereRaw('DATE_ADD(date(startDate), INTERVAL duration DAY) >=  CURDATE()')
        //             ->count();
        //         $totalBatch = count($totalBatchData);
        //         $totalTrainer = ProvidersTrainer::where('provider_id', $provider_id)->count();
        //         $totalStudent = 0;
        //         if (count($totalBatchData) > 0) {

        //             foreach ($totalBatchData as $trainee) {

        //                 $student = TrainingApplicant::where('BatchId', $trainee->id)->where('IsTrainee', 1)->first();
        //                 if ($student) {
        //                     $totalStudent = $totalStudent + 1;
        //                 }
        //             }
        //         }
        //     } else {
        //         $totalBatch = TrainingBatch::count();
        //         $runningBatch = TrainingBatch::whereNotNull('startDate')
        //             ->whereRaw('date(startDate) <=  CURDATE()')
        //             ->whereRaw('DATE_ADD(date(startDate), INTERVAL duration DAY) >=  CURDATE()')
        //             ->count();
        //         $totalTrainer = TrainerProfile::count();
        //         $totalStudent = TrainingApplicant::where('IsTrainee', 1)
        //             ->count();

        //     }

        //     $totalProvider = Provider::count();
        //     $totalCourse = Training::count();
        //     //$totalProdiver = Provider::count();

        //     $totalCoordinator = 0;


        //     $totalPresentToday = BatchScheduleDetail::whereRaw('date=CURDATE()')->get();
        //     $totalPresent = 0;
        //     if (count($totalPresentToday) > 0) {
        //         foreach ($totalPresentToday as $row) {
        //             $present = ClassAttendance::where('batch_schedule_detail_id', $row['id'])->where('is_present', 1)->get();
        //             if ($present) {
        //                 $totalPresent = count($present) + $totalPresent;
        //             }
        //         }
        //     }
        //     $todaysTotalPresent = $totalPresent;
        //     return response()->json([
        //         'success' => true,
        //         'data' => [
        //             'totalDisision' => $totalDisision,
        //             'totalDistrict' => 44,
        //             'totalUpazila' => 130,
        //             'totalBatch' => $totalBatch,
        //             'totalStudent' => $totalStudent,
        //             'totalProvider' => $totalProvider,
        //             'totalCourse' => $totalCourse,
        //             'runningBatch' => $runningBatch,
        //             'completeBatch' => 0,
        //             'totalTrainer' => $totalTrainer,
        //             'totalCoordinator' => $totalCoordinator,
        //             'totalPresentToday' => $todaysTotalPresent,
        //         ],
        //     ]);
        // } catch (\Exception $e) {

        //     return response()->json([
        //         'success' => false,
        //         'message' => $e->getMessage(),
        //     ]);
        // }

        return "remove this";
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

        $query = TrainingBatch::with('trainingBatchSchedule', 'getTraining', 'getTraining.trainingTitle');

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

        return response()->json(['data' => $items, 'success' => true]);
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
        return response()->json(['data' => $items, 'success' => true]);
    }

    public function getAllupazilas(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search', '');
        $query = Upazila::with('district');

        if ($search) {
            $query->where('Name', 'like', "%$search%")
                ->orWhere('NameEng', 'like', "%$search%")
                ->orWhere('Code', 'like', "%$search%");
        }
        $items = $query->paginate($perPage);
        return response()->json(['data' => $items, 'success' => true]);
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
        $user = auth()->user();
        $userType = $this->authUser($user->email);

        $data = [];
        try {
            if (strtolower($userType->role->name) == "superadmin") {
                $data['total_batch'] = TrainingBatch::count();
                $data['running_batch'] = TrainingBatchSchedule::whereHas('isStatus1')
                    ->whereHas('isStatus2')
                    ->count();

                $data['completed_batch'] = TrainingBatchSchedule::whereHas('isStatus3')
                    ->doesntHave('isStatus2')
                    ->doesntHave('isStatus1')
                    ->count();

                $data['pending_class'] = BatchScheduleDetail::where('status', 1)
                    ->count();
                $data['running_class'] = BatchScheduleDetail::where('status', 2)
                    ->count();

                $data['complete_class'] = BatchScheduleDetail::where('status', 3)
                    ->count();



                $data['total_trainee'] = TrainingApplicant::where('isTrainee', 1)
                    ->count();

                $data['total_attend_today'] = ClassAttendance::where('is_present', 1)
                    ->whereHas('scheduleDetail', function ($query) {
                        $query->where('date', Carbon::now()->format('Y-m-d'));
                    })
                    ->count();
                $data['total_absent_today'] = ClassAttendance::where('is_present', '0')
                    ->whereHas('scheduleDetail', function ($query) {
                        $query->where('date', Carbon::now()->format('Y-m-d'));
                    })
                    ->count();

                $data['total_attend_week'] = ClassAttendance::where('is_present', 1)
                    ->whereHas('scheduleDetail', function ($query) {
                        $query->where('date', '>=', Carbon::now()->subWeek()->format('Y-m-d'));
                    })
                    ->count();

                $data['total_absent_week'] = ClassAttendance::where('is_present', '0')
                    ->whereHas('scheduleDetail', function ($query) {
                        $query->where('date', '>=', Carbon::now()->subWeek()->format('Y-m-d'));
                    })
                    ->count();

                $data['total_attend_month'] = ClassAttendance::where('is_present', 1)
                    ->whereHas('scheduleDetail', function ($query) {
                        $query->where('date', '>=', Carbon::now()->subMonth()->format('Y-m-d'));
                    })
                    ->count();

                $data['total_absent_month'] = ClassAttendance::where('is_present', '0')
                    ->whereHas('scheduleDetail', function ($query) {
                        $query->where('date', '>=', Carbon::now()->subMonth()->format('Y-m-d'));
                    })
                    ->count();
                $data['total_trainer'] = ProvidersTrainer::count() ?? 0;
                $data['total_dropout'] = TrainingApplicant::where('isDroppedOut', 1)
                    ->count();

                $data['total_vendor'] = Provider::count();
                $data['total_allownce_paid'] = 0;
                $data['total_allownce_remaining'] = 0;
                $data['total_freelancer'] = 0;

                $data['total_upazila'] = TrainingBatch::selectRaw('COUNT(DISTINCT(RIGHT(GEOCode, 2))) as total_upazila')->first()->total_upazila ?? 0;

                $data['total_division'] = TrainingBatch::selectRaw('COUNT(DISTINCT(LEFT(GEOCode, 2))) as total_division')->first()->total_division ?? 0;

                $data['total_district'] = TrainingBatch::selectRaw('COUNT(DISTINCT(SUBSTRING(GEOCode, 4,2))) AS total_district')->first()->total_district ?? 0;


            } elseif (strtolower($userType->role->name) == "admin") {
                // division 


            } elseif (strtolower($userType->role->name) == "dpd") {


            } elseif (strtolower($userType->role->name) == "dc") {
                ///

            } elseif (strtolower($userType->role->name) == "uno") {

            } elseif (strtolower($userType->role->name) == "piuofficer") {

            } elseif (strtolower($userType->role->name) == "member") {

            } elseif (strtolower($userType->role->name) == "trainee") {

            } elseif (strtolower($userType->role->name) == "trainer") {

                $profile_id = $userType->ProfileId;

                $data['total_batch'] = TrainingBatch::whereHas('providerTrainers', function ($query) use ($profile_id) {
                    $query->where('ProfileId', $profile_id);
                })->count();

                $data['running_batch'] = TrainingBatchSchedule::whereHas('isStatus1')
                    ->whereHas('isStatus2')
                    ->whereHas('trainingBatch.providerTrainers', function ($query) use ($profile_id) {
                        $query->where('ProfileId', $profile_id);
                    })
                    ->count();
                $data['completed_batch'] = TrainingBatchSchedule::whereHas('isStatus3')
                    ->doesntHave('isStatus2')
                    ->doesntHave('isStatus1')
                    ->whereHas('trainingBatch.providerTrainers', function ($query) use ($profile_id) {
                        $query->where('ProfileId', $profile_id);
                    })
                    ->count();


                $data['pending_class'] = BatchScheduleDetail::where('status', 1)
                    ->whereHas('schedule.trainingBatch.providerTrainers', function ($query) use ($profile_id) {
                        $query->where('ProfileId', $profile_id);
                    })
                    ->count();
                $data['running_class'] = BatchScheduleDetail::where('status', 2)
                    ->whereHas('schedule.trainingBatch.providerTrainers', function ($query) use ($profile_id) {
                        $query->where('ProfileId', $profile_id);
                    })
                    ->count();
                $data['complete_class'] = BatchScheduleDetail::where('status', 3)
                    ->whereHas('schedule.trainingBatch.providerTrainers', function ($query) use ($profile_id) {
                        $query->where('ProfileId', $profile_id);
                    })
                    ->count();

                $data['total_dropout'] = TrainingApplicant::where('isDroppedOut', 1)
                    ->whereHas('trainingBatch.providerTrainers', function ($query) use ($profile_id) {
                        $query->where('ProfileId', $profile_id);
                    })
                    ->count();



                $data['total_trainee'] = TrainingApplicant::where('isTrainee', 1)
                    ->whereHas('trainingBatch.providerTrainers', function ($query) use ($profile_id) {
                        $query->where('ProfileId', $profile_id);
                    })
                    ->count();

                $data['total_attend_today'] = ClassAttendance::where('is_present', 1)
                    ->whereHas('scheduleDetail', function ($query) {
                        $query->where('date', Carbon::now()->format('Y-m-d'));
                    })
                    ->whereHas('scheduleDetail.schedule.trainingBatch.providerTrainers', function ($query) use ($profile_id) {
                        $query->where('ProfileId', $profile_id);
                    })
                    ->count();

                $data['total_absent_today'] = ClassAttendance::where('is_present', '0')
                    ->whereHas('scheduleDetail', function ($query) {
                        $query->where('date', Carbon::now()->format('Y-m-d'));
                    })
                    ->whereHas('scheduleDetail.schedule.trainingBatch.providerTrainers', function ($query) use ($profile_id) {
                        $query->where('ProfileId', $profile_id);
                    })
                    ->count();

            } elseif (strtolower($userType->role->name) == "Inspector") {


            } elseif (strtolower($userType->role->name) == "provider") {
                // A)Total batch b) Running batch c) Total Course coordinator d) Total Trainer e) Total trainee f) Course material g) Successful Freelancer h) Present trainee today 
                $provider_id = $userType->provider_id;

                $data['total_batch'] = TrainingBatch::where('provider_id', $provider_id)->count();

                $data['running_batch'] = TrainingBatchSchedule::whereHas('isStatus1')
                    ->whereHas('isStatus2')
                    ->whereHas('trainingBatch', function ($query) use ($provider_id) {
                        $query->where('provider_id', $provider_id);
                    })
                    ->count();

                $data['completed_batch'] = TrainingBatchSchedule::whereHas('isStatus3')
                    ->doesntHave('isStatus2')
                    ->doesntHave('isStatus1')
                    ->whereHas('trainingBatch', function ($query) use ($provider_id) {
                        $query->where('provider_id', $provider_id);
                    })
                    ->count();

                $data['pending_class'] = BatchScheduleDetail::where('status', 1)
                    ->whereHas('schedule.trainingBatch', function ($query) use ($provider_id) {
                        $query->where('provider_id', $provider_id);
                    })
                    ->count();
                $data['running_class'] = BatchScheduleDetail::where('status', 2)
                    ->whereHas('schedule.trainingBatch', function ($query) use ($provider_id) {
                        $query->where('provider_id', $provider_id);
                    })
                    ->count();

                $data['complete_class'] = BatchScheduleDetail::where('status', 3)
                    ->whereHas('schedule.trainingBatch', function ($query) use ($provider_id) {
                        $query->where('provider_id', $provider_id);
                    })
                    ->count();

                $data['total_dropout'] = TrainingApplicant::where('isDroppedOut', 1)
                    ->whereHas('trainingBatch', function ($query) use ($provider_id) {
                        $query->where('provider_id', $provider_id);
                    })
                    ->count();


                $data['total_coordinator'] = 0;
                $data['total_trainer'] = ProvidersTrainer::where('provider_id', $provider_id)
                    ->selectRaw('COUNT(DISTINCT(ProfileId)) as total_trainer')
                    ->first()->total_trainer ?? 0;

                $data['total_trainee'] = TrainingApplicant::where('isTrainee', 1)
                    ->whereHas('trainingBatch', function ($query) use ($provider_id) {
                        $query->where('provider_id', $provider_id);
                    })
                    ->count();

                $data['course_material'] = 0;
                $data['total_freelancer'] = 0;

                $data['total_attend_today'] = ClassAttendance::where('is_present', 1)
                    ->whereHas('scheduleDetail', function ($query) {
                        $query->where('date', Carbon::now()->format('Y-m-d'));
                    })
                    ->whereHas('scheduleDetail.schedule.trainingBatch', function ($query) use ($provider_id) {
                        $query->where('provider_id', $provider_id);
                    })
                    ->count();
                $data['total_absent_today'] = ClassAttendance::where('is_present', '0')
                    ->whereHas('scheduleDetail', function ($query) {
                        $query->where('date', Carbon::now()->format('Y-m-d'));
                    })
                    ->whereHas('scheduleDetail.schedule.trainingBatch', function ($query) use ($provider_id) {
                        $query->where('provider_id', $provider_id);
                    })
                    ->count();

            } elseif (strtolower($userType->role->name) == "coordinator") {


            } else {
                # code...
            }


            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            // Handle the exception, log it, or return an error response
            return response()->json(['error' => 'An error occurred while processing the request.']);
        }

    }
}
