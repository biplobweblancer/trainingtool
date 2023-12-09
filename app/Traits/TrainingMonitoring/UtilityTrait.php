<?php

namespace App\Traits\TrainingMonitoring;

use App\Models\TrainingMonitoring\FinalSelection;
use App\Models\TrainingMonitoring\PrelimarySelection;
use App\Models\TrainingMonitoring\UserType;

trait UtilityTrait
{
    public function selectionStatusAdd($users)
    {
        $finalSelectionUserIds = FinalSelection::pluck('user_id')->all();
        $preliminarySelectionUserIds = PrelimarySelection::pluck('user_id')->all();

        foreach ($users as $user) {
            if (in_array($user->user_id, $finalSelectionUserIds)) {
                $user->selection_status = 'final';
            } elseif (in_array($user->user_id, $preliminarySelectionUserIds) && $user->selection_status !== 'final') {
                $user->selection_status = 'preliminary';
            } else {
                $user->selection_status = 'registered';
            }
        }

        return $users;
    }

    public function selectionStatusAddPre($users)
    {
        $finalSelectionUserIds = FinalSelection::pluck('user_id')->all();

        foreach ($users as $user) {
            if (in_array($user->user_id, $finalSelectionUserIds)) {
                $user->selection_status = 'final';
            } else {
                $user->selection_status = 'preliminary';
            }
        }

        return $users;
    }

    public function generatePercentage($user)
    {
        $percentage = 0;
        if (!is_null($user)) {
            if (!is_null($user->fname)) {
                $percentage += 10;
            }
            if (!is_null($user->lname)) {
                $percentage += 10;
            }
            if (!is_null($user->username)) {
                $percentage += 10;
            }
            if (!is_null($user->email)) {
                $percentage += 10;
            }
            if (!is_null($user->phone_number)) {
                $percentage += 10;
            }
            if (!is_null($user->password)) {
                $percentage += 10;
            }
            if (!is_null($user->gender)) {
                $percentage += 10;
            }
            if (!is_null($user->address)) {
                $percentage += 10;
            }
            if (!is_null($user->dob)) {
                $percentage += 10;
            }
            if (!is_null($user->photo_url)) {
                $percentage += 10;
            }
        }
        return $percentage;
    }

    public function getTestExample($string)
    {
        return strtolower(preg_replace(
            ['/[^\w\s]+/', '/\s+/'],
            ['', '-'],
            $string
        ));
    }

    public function getDay($date)
    {
        $created_date = date('Y-m-d 00:00:01', strtotime($date));
        $startTimeStamp = strtotime($created_date);
        $endTimeStamp = strtotime(date('Y-m-d 23:59:59'));
        $timeDiff = abs($endTimeStamp - $startTimeStamp);
        $numberDays = $timeDiff / 86400;
        $numberDays = intval($numberDays);
        return $numberDays;
    }

    public function deleteLocalFile($filePath)
    {
        if (file_exists($filePath)) {
            if (unlink($filePath)) {
                return true; // File deleted successfully
            } else {
                return false; // Failed to delete the file
            }
        } else {
            return true; // File does not exist, consider it already deleted
        }
    }

    public function getExamType()
    {
        $examType = array();
        $examType[0] = 'Multiple';
        $examType[1] = 'Question';
        $examType[2] = 'FillInTheGrap';
        $examType[3] = 'Assignment';
        return $examType;
    }

    public function getExamDuration()
    {
        $examDuration = array();
        $examDuration[0] = '10minute';
        $examDuration[1] = '20minute';
        $examDuration[2] = '30minute';
        $examDuration[3] = '40minute';
        $examDuration[4] = '50minute';
        $examDuration[5] = '60minute';
        $examDuration[6] = '90minute';
        $examDuration[7] = '2hour';
        $examDuration[8] = '3hour';
        $examDuration[9] = '1day';
        $examDuration[10] = '2day';
        $examDuration[11] = '3day';
        $examDuration[12] = '4day';
        $examDuration[13] = '1week';
        return $examDuration;
    }

    public function getExamMarks()
    {
        $examMarks = array();
        $examMarks[0] = '10.00';
        $examMarks[1] = '20.00';
        $examMarks[2] = '30.00';
        $examMarks[3] = '40.00';
        $examMarks[4] = '50.00';
        $examMarks[5] = '60.00';
        $examMarks[6] = '100.00';
        return $examMarks;
    }

    public function getMentorInstructor($courseEnrollUsers)
    {
        $mentorInstructor = array_filter($courseEnrollUsers, function ($enrollUser) {
            return $enrollUser["role"] === "mentor" || $enrollUser["role"] === "instructor";
        });

        return $mentorInstructor;
    }

    public function getClassDuration()
    {
        $classDuration = array();
        $classDuration[30] = '30minute';
        $classDuration[45] = '45minute';
        $classDuration[60] = '1hour';
        $classDuration[75] = '1hour15minute';
        $classDuration[90] = '1hour30minute';
        $classDuration[105] = '1hour45minute';
        $classDuration[120] = '2hour';
        $classDuration[135] = '2hour15minute';
        $classDuration[150] = '2hour30minute';
        $classDuration[165] = '2hour45minute';
        $classDuration[180] = '3hour';
        $classDuration[195] = '3hour15minute';
        $classDuration[210] = '3hour30minute';
        $classDuration[225] = '3hour45minute';
        $classDuration[240] = '4hour';
        $classDuration[255] = '4hour15minute';
        $classDuration[270] = '4hour30minute';
        $classDuration[285] = '4hour45minute';
        $classDuration[300] = '5hour';
        $classDuration[315] = '5hour15minute';
        $classDuration[330] = '5hour30minute';
        $classDuration[345] = '5hour45minute';
        $classDuration[360] = '6hour';
        $classDuration[375] = '6hour15minute';
        $classDuration[390] = '6hour30minute';
        $classDuration[405] = '6hour45minute';
        $classDuration[420] = '7hour';
        $classDuration[435] = '7hour15minute';
        $classDuration[450] = '7hour30minute';
        $classDuration[465] = '7hour45minute';
        $classDuration[480] = '8hour';
        $classDuration[495] = '8hour15minute';
        $classDuration[510] = '8hour30minute';
        $classDuration[525] = '8hour45minute';
        $classDuration[540] = '9hour';
        $classDuration[555] = '9hour15minute';
        $classDuration[570] = '9hour30minute';
        $classDuration[585] = '9hour45minute';
        $classDuration[600] = '10hour';

        return $classDuration;
    }

    public function authUser($email){
       return $userType = UserType::with('profile','role')
                    ->whereHas('profile', function ($query) use ($email) {
                        $query->where('Email',$email);
                    })->first();
        
    }
}
