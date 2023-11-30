<?php

namespace App\Repositories\TrainingMonitoring;

use App\Models\TrainingMonitoring\SubCategory;
use App\Repositories\TrainingMonitoring\Interfaces\SubCategoryRepositoryInterface;

class SubCategoryRepository implements SubCategoryRepositoryInterface
{
    public function all($id = null)
    {
        if ($id) {
            $sub_categories = SubCategory::with('category')
                ->where('category_id', $id)
                ->get();
        } else {
            $sub_categories = SubCategory::with('category')->get();
        }
        return $sub_categories;
    }

    public function store($data)
    {
        return SubCategory::create($data);
    }

    public function details($id)
    {
        return SubCategory::with('category')->where('id', '=', $id)->first();
    }

    public function find($id)
    {
        return SubCategory::find($id);
    }

    public function update($subCategory, $data)
    {
        $subCategory->update($data);
    }

    public function delete($id)
    {
        return SubCategory::find($id);
    }
}
