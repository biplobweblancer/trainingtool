<?php

namespace App\Repositories\TrainingMonitoring;

use App\Models\TrainingMonitoring\District;
use App\Repositories\TrainingMonitoring\Interfaces\DistrictRepositoryInterface;

class DistrictRepository implements DistrictRepositoryInterface
{
    public function all($division_code = null)
    {

        if ($division_code) {
            $districts = District::with('division')
                ->whereHas('division', function ($query, $division_code) {
                    $query->where('Code', $division_code);
                })
                ->get();
        } else {
            $districts = District::with('division')->get();
        }

        return $districts;
    }

    public function store($data)
    {
        return District::create($data);
    }

    public function details($id)
    {
        return District::with('division')->where('id', '=', $id)->first();
    }

    public function find($id)
    {
        return District::find($id);
    }

    public function update($district, $data)
    {
        $district->update($data);
    }

    public function delete($id)
    {
        return District::find($id);
    }
}
