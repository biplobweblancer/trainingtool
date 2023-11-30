<?php

namespace App\Http\Controllers\Api\TrainingMonitoring;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\Models\Training\TraineePrerequisite;
use App\Models\Training\TrainingSession;
use App\Models\Training\Training;
use Exception;

class TrainingController extends Controller
{
    public function clientIndex(Request $request){
        $providerId = $request->all()['providerId'];
        $vendorId = $request->all()['vendorId'];
        // return $vendorId;
        if(empty($providerId) && empty($vendorId)){
            return response(['message' => 'Invalid input.'], 402);
        }
        $trainings = Training::where(['trainingProviderOrgId' => $providerId , 'VendorId' => $vendorId])
            ->get()
            ->map(function($training, $key){
                return [
                    'id' => $training->id,
                    'slug' => $training->slug
                ];
             }
            );
        return response(['message' => 'Yes, this is from training-monitoring-route','data' => $trainings],200);
    }

    public function getBatchesByTraining(Request $request){
        $providerId = $request->all()['providerId'];
        $vendorId = $request->all()['vendorId'];
        // return $vendorId;
        if(empty($providerId) && empty($vendorId)){
            return response(['message' => 'Invalid input.'], 402);
        }
        $trainings = Training::where(['trainingProviderOrgId' => $providerId , 'VendorId' => $vendorId])
            ->get()
            ->map(function($training, $key){
                return [
                    'id' => $training->id,
                    'slug' => $training->slug
                ];
             }
            );
        return $trainings;
    }
    
    public function getAvailableTrainingBatches(Request $request){
        $geoCode = $request->get('GEOCode');
        $trainingId = $request->get('trainingId');
        $sql = "
            select tb.*, p.KnownAs as Name,  p.KnownAsBangla as NameBN, p.PhotoUrl 
            from training_batches tb  
            left outer join profiles p on (p.id = tb.LeadTrainerProfileId)
            left outer join trainer_profiles tp on (tp.ProfileId = tb.LeadTrainerProfileId)
            WHERE tb.trainingId = ? and (tb.isCompleted IS NULL OR tb.isCompleted='0') and ((DATE_ADD(tb.lastApplicationDate, INTERVAL 1 DAY) > now()) OR tb.lastApplicationDate IS NULL) and tb.isActive='1'
        ";
        if(!empty($geoCode)){
            $sql = $sql ." and tb.GEOCode LIKE '{$geoCode}%'";
        }
        $result = DB::connection('mysql-soms')->select($sql, [$trainingId]);
        return response(['success' => true, 'message' => 'Available Running Batch list', 'data' => $result], 200);
    }

    public function getAvailableTrainings(Request $request){
        $areaId = $request->get('areaId');
        $name =  $request->get('name');
        $geoCode = $request->get('GEOCode');
        // user_social_profiles.facebook, user_social_profiles.youtube, user_social_profiles.linkedin, user_social_profiles.twitter,
        // left join user_social_profiles usp on (usp.ProfileId = rtl.LeadTrainerProfileId)
        $sql = "select rt.*, tt.Name as title, rtl.id AS training_id, rtl.*, profiles.KnownAs as trainer, profiles.PhotoUrl as trainerPhotoUrl,
        trainer_profiles.professionalBio as professionalBio, tp.Name as provider
        from trainings as rt
        inner join training_titles tt on rt.titleId = tt.id
        inner join training_batches rtl on rtl.trainingId = rt.id
        left join profiles on (profiles.id = rtl.LeadTrainerProfileId)
        left join trainer_profiles on (trainer_profiles.ProfileId = rtl.LeadTrainerProfileId)
        
        left join training_providers tp on (tp.id = rt.trainingProviderOrgId)
            where rtl.isActive = 1 and rtl.isCompleted = 0";
        if(!empty($geoCode)){
            $sql = $sql ." and rtl.GEOCode LIKE '{$geoCode}%'";
        }
        if(!empty($areaId))
            $sql = $sql ." and TrainingAreaId = '{$areaId}'";
        if(!empty($name))
            $sql = $sql ." and Name like '%{$name}%'";

        $result = DB::connection('mysql-soms')->select($sql);

        $finalResult = [];
        $holdIds = [];
        foreach ($result as $key => $value) {
            if (!in_array($value->id, $holdIds)) {
                $holdIds[] = $value->id;
                $finalResult[$value->id] = $value;
            }
            // $finalResult[$value->id]->sessionSummary[] = [
            //     'title' => $value->sessionTitle,
            //     'description' => $value->sessionDescription
            // ];
            // unset($finalResult[$value->id]->sessionTitle);
            // unset($finalResult[$value->id]->sessionDescription);
        }

        return response(['success' => true, 'message' => 'Available Running Training list', 'data' => $finalResult], 200);
    }
    public function getTopPageTrainings(){
        
        // user_social_profiles.facebook, user_social_profiles.youtube, user_social_profiles.linkedin, user_social_profiles.twitter,
        // left join user_social_profiles usp on (usp.ProfileId = rtl.LeadTrainerProfileId)
        $sql = "select distinct rt.*, tt.Name as title, tp.Name as provider
        from trainings as rt
        inner join training_titles tt on rt.titleId = tt.id
        inner join training_batches rtl on rtl.trainingId = rt.id
        left join training_providers tp on (tp.id = rt.trainingProviderOrgId)
        where rt.isActive = 1 and rt.isPublicTraining = 1 and rtl.isActive = 1 and rtl.isCompleted = 0";        

        $result = DB::connection('mysql-soms')->select($sql);

        return response(['success' => true, 'message' => 'Available Running Training list', 'data' => $result], 200);
    }
    public function getSkillAreasByAvailableTrainings(){
        $sql = "SELECT distinct sa.id, sa.Name FROM training_wise_skills ts
        inner join trainings rt on rt.id=ts.trainingId
        inner join skills s on s.id = ts.skillId
        inner join skillarea_tagging st on st.SkillId = ts.skillId
        inner join skill_areas sa on sa.id = st.SkillAreaId";
        $result = DB::connection('mysql-soms')->select($sql);
        return response(['success' => true, 'message' => 'Training Skill Areas by Available Trainings', 'data' => $result], 200);
    }
    public function getTrainingDetails($id){
        
        // left join training_sessions as rts on (rt.id = rts.trainingId)
        // left join trainee_prerequisits as tpr on (rt.id = tpr.trainingId)
        //tpr.description as prerequisite, tpr.isHeader, tpr.isOptional,
        $sql = "select rt.*, rt.id as trainingId, tt.Name as title, tp.Name as provider
        from trainings as rt
        inner join training_titles tt on rt.titleId = tt.id
        left join training_providers tp on (tp.id = rt.trainingProviderOrgId)
        where rt.id = ?";
        
        $result = DB::connection('mysql-soms')->select($sql,[$id])[0];
        $trainingSessions = TrainingSession::where('trainingId', $result->trainingId)->get();
        $result->sessionSummary = $trainingSessions;
        
        $prerequisites = TraineePrerequisite::where('trainingId', $result->trainingId)->get();
        $result->prerequisites = $prerequisites;
        
        

        return response(['success' => true, 'message' => 'Training Details', 'data' => $result], 200);
    }
    

    public function getTrainingDetailsByCode($code){
        
        // left join training_sessions as rts on (rt.id = rts.trainingId)
        // left join trainee_prerequisits as tpr on (rt.id = tpr.trainingId)
        //tpr.description as prerequisite, tpr.isHeader, tpr.isOptional,
        $sql = "select rt.*, rt.id as trainingId, tt.Name as title, rtl.id AS batchId, rtl.*, 
        profiles.KnownAs as trainer, profiles.PhotoUrl as trainerPhotoUrl,
        trainer_profiles.professionalBio as professionalBio,
        
        tp.Name as provider
        from trainings as rt
        inner join training_titles tt on rt.titleId = tt.id
        inner join training_batches rtl on rtl.trainingId = rt.id
        
        left join profiles on (profiles.id = rtl.LeadTrainerProfileId)
        left join trainer_profiles on (trainer_profiles.ProfileId = rtl.LeadTrainerProfileId)
        left join user_social_profiles usp on (usp.ProfileId = rtl.LeadTrainerProfileId)
        left join training_providers tp on (tp.id = rt.trainingProviderOrgId)
        where rtl.batchCode = ?";
        try{
            $result = DB::connection('mysql-soms')->select($sql,[$code])[0];
            $trainingSessions = TrainingSession::where('trainingId', $result->trainingId)->get();
            $result->sessionSummary = $trainingSessions;
            
            $prerequisites = TraineePrerequisite::where('trainingId', $result->trainingId)->get();
            $result->prerequisites = $prerequisites;
            return response(['success' => true, 'message' => 'Training Details', 'data' => $result], 200);
        }catch(Exception $e){
            return response(['success' => false, 'message' => 'Sorry! Training details not found.', 'data' => null], 200);
        }
        
    }
    public function getMyCourses()
    {
        $query = "
        select distinct rtl.*, tt.Name as title, e.*, tp.Name as TrainingProvider, t.duration
        from training_applicants e
        inner join training_batches rtl on rtl.id = e.BatchId
        inner join trainings t on rtl.trainingId = t.id
        inner join training_titles tt on e.TrainingTitleId = tt.id
        left outer join training_providers tp on rtl.TrainingProviderOrgId = tp.id
        where e.ProfileId = ?
    ";
    $courses = DB::connection('mysql-soms')->select($query, [request()->ProfileId]);
        return response(['success' => true, 'message' => 'My Courses', 'data' => ['courses' => $courses]], 200);
    }

    // GEO Locations
    public function getActiveDivisions($trainingId){
        $sql = "
            select t.*,d.Name, d.NameEng from (
            select DISTINCT SUBSTRING(GEOCode, 1, 2) AS Code from training_batches  
            where (DATE_ADD(lastApplicationDate, INTERVAL 1 DAY) > now() OR lastApplicationDate IS NULL)";
        if($trainingId){
            $sql = $sql." and trainingId = ? ";
        }        

        $sql = $sql."     ) t inner join geodivisions d on t.Code = d.Code ";
        if($trainingId){
            $divisions = DB::connection('mysql-soms')->select($sql, [$trainingId]);
        }else{
            $divisions = DB::connection('mysql-soms')->select($sql);
        }

        return response(['success' => true, 'message' => 'Active divisions', 'data' => ['divisions' => $divisions]], 200);
    }

    public function getActiveDistricts($parentCode, $trainingId){
        $sql = "
            select t.*, d.Name, d.NameEng from (
            select DISTINCT SUBSTRING(GEOCode, 4, 2) AS Code from training_batches  
            where (DATE_ADD(lastApplicationDate, INTERVAL 1 DAY) > now() OR lastApplicationDate IS NULL)";
        if($trainingId){
            $sql = $sql." and trainingId = ? ";
        }        
        if($parentCode){
            $sql = $sql." and SUBSTRING(GEOCode, 1, 2) = '".$parentCode."'";
        }
        $sql = $sql."     ) t inner join geodistricts d on t.Code = d.Code ";
        if($trainingId){
            $districts = DB::connection('mysql-soms')->select($sql, [$trainingId]);
        }else{
            $districts = DB::connection('mysql-soms')->select($sql);
        }

        return response(['success' => true, 'message' => 'Active districts', 'data' => ['districts' => $districts]], 200);
    }

    public function getActiveUpazilas($parentCode, $trainingId){
        $sql = "
            select t.*, d.Name, d.NameEng from (
            select DISTINCT SUBSTRING(GEOCode, 7, 2) AS Code from training_batches  
            where (DATE_ADD(lastApplicationDate, INTERVAL 1 DAY) > now() OR lastApplicationDate IS NULL)";
        if($trainingId){
            $sql = $sql." and trainingId = ? ";
        }        
        if($parentCode){
            $sql = $sql." and SUBSTRING(GEOCode, 4, 2) = '".$parentCode."'";
        }
        $sql = $sql."     ) t inner join geoupazilas d on t.Code = d.Code and d.ParentCode = '". $parentCode ."'";
        if($trainingId){
            $upazilas = DB::connection('mysql-soms')->select($sql, [$trainingId]);
        }else{
            $upazilas = DB::connection('mysql-soms')->select($sql);
        }
        // return $sql;
        return response(['success' => true, 'message' => 'Active upazilas', 'data' => ['upazilas' => $upazilas]], 200);
    }

}
