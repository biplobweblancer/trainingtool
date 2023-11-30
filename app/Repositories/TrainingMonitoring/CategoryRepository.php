<?php

namespace App\Repositories\TrainingMonitoring;

use App\Models\TrainingMonitoring\Category;
use App\Repositories\TrainingMonitoring\Interfaces\CategoryRepositoryInterface;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function all()
    {
        return Category::all();
    }

    public function store($data)
    {

        return Category::create($data);
    }

    public function details($id)
    {
        return Category::where('id', '=', $id)->first();
    }

    public function find($id){

        return Category::find($id);
        
    }

    public function update($category, $data)
    {
        $category->update($data);
    }

    public function delete($id)
    {
        return Category::find($id);
    }
    
}
