<?php

namespace App\Repositories\TrainingMonitoring;

use App\Models\TrainingMonitoring\FinalSelection;
use App\Models\TrainingMonitoring\PrelimarySelection;
use App\Models\TrainingMonitoring\User;
use App\Repositories\TrainingMonitoring\Interfaces\FinalSelectionRepositoryInterface;
use Exception;

class FinalSelectionRepository implements FinalSelectionRepositoryInterface
{
    public function all()
    {
    }

    public function store($all_selected_users, $auth_id)
    {
        try {
            foreach ($all_selected_users as $value) {
                $user = User::find($value);
                $preliminarySelection = PrelimarySelection::where('user_id', $value)->first();

                if (!$preliminarySelection) {
                    throw new Exception('UserDetail not found for user id: ' . $value);
                }

                $already_selected = FinalSelection::where('user_id', $value)->first();

                if ($already_selected) {
                    throw new Exception('User already exits: ' . $user->fname . " " . $user->lname);
                }

                $data = [
                    'user_id' => $value,
                    'category_id' => $preliminarySelection->category_id,
                    'sub_category_id' => $preliminarySelection->sub_category_id,
                    'is_selected' => 1,
                    'district_id' => 1,
                    'upazila_id' => 1,
                    'selection_date' => now(),
                    'committee_id' => $auth_id,
                ];

                FinalSelection::create($data);
            }
        } catch (\Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
