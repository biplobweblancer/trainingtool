<?php

namespace App\Repositories\TrainingMonitoring;

use App\Models\TrainingMonitoring\Committee;
use App\Repositories\TrainingMonitoring\Interfaces\CommitteeRepositoryInterface;

class CommitteeRepository implements CommitteeRepositoryInterface
{
    public function all()
    {
        return Committee::get();
    }

    public function store($data)
    {
        return Committee::create($data);
    }

    public function details($id)
    {
        return Committee::where('id', '=', $id)->first();
    }

    public function find($id){
        return Committee::find($id);        
    }

    public function update($committee, $data)
    {
        $committee->update($data);
    }

    public function delete($id)
    {
        return Committee::find($id);
    }
    
}
