<?php

namespace App\Repositories\TrainingMonitoring;

use App\Models\TrainingMonitoring\Division;
use App\Repositories\TrainingMonitoring\Interfaces\DivisionRepositoryInterface;

class DivisionRepository implements DivisionRepositoryInterface
{
    public function all()
    {
        return Division::all();
    }

    public function store($data)
    {

        return Division::create($data);
    }

    public function details($id)
    {
        return Division::where('id', '=', $id)->first();
    }

    public function find($id){

        return Division::find($id);
        
    }

    public function update($division, $data)
    {
        $division->update($data);
    }

    public function delete($id)
    {
        return Division::find($id);
    }
    
}
