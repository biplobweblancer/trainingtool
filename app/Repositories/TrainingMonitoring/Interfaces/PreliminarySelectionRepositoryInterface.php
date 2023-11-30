<?php

namespace App\Repositories\TrainingMonitoring\Interfaces;

interface PreliminarySelectionRepositoryInterface
{
    public function all();
    
    public function store($all_selected_users, $auth_id);
}
