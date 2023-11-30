<?php

namespace App\Repositories\TrainingMonitoring;

use App\Models\TrainingMonitoring\District;
use App\Models\TrainingMonitoring\User;
use App\Models\TrainingMonitoring\UserDetail;
use App\Repositories\TrainingMonitoring\Interfaces\UserDetailRepositoryInterface;
use App\Traits\TrainingMonitoring\UtilityTrait;

class UserDetailRepository implements UserDetailRepositoryInterface
{
    use UtilityTrait;

    /**
     * 
     * All users who completed their profile
     * 
     */
    public function all()
    {
        $all_user_details = UserDetail::with('user', 'user.role', 'district', 'upazila', 'category', 'subCategory')
            ->get();

        return $all_user_details;
    }

    /**
     * 
     * Find specific user details
     * 
     */
    public function find($id)
    {
        return UserDetail::where('user_id', $id)->first();
    }

    public function update($data, $user)
    {
        $userDetails = UserDetail::where('user_id', $user->id)->first();
        if ($userDetails) {
            $imagefilename = $userDetails->image_url;
            $imagefilePath = public_path('uploads/images/') . $imagefilename;

            if (isset($data['image_url']) && $data['image_url'] !== null) {
                if ($imagefilename != null) {
                    $this->deleteLocalFile($imagefilePath);
                }
                $file = $data['image_url'];
                $filename = date('YmdHi') . $file->getClientOriginalName();
                $file->move(public_path('uploads/images'), $filename);
                $data['image_url'] = $filename;
            } else {
                if ($imagefilename != null) {
                    $this->deleteLocalFile($imagefilePath);

                    $data['image_url'] = null;
                }
            }
            // hsc
            $hscfilename = $userDetails->hsc_certificate;
            $hscfilePath = public_path('uploads/hsc/') . $hscfilename;
            if (isset($data['hsc_certificate'])) {
                if ($hscfilename != null) {
                    $this->deleteLocalFile($hscfilePath);
                }
                $file = $data['hsc_certificate'];
                $filename = date('YmdHi') . $file->getClientOriginalName();
                $file->move(public_path('uploads/hsc'), $filename);
                $data['hsc_certificate'] = $filename;
            }

            $sscfilename = $userDetails->ssc_certificate;
            $sscfilePath = public_path('uploads/ssc/') . $sscfilename;
            if (isset($data['ssc_certificate'])) {
                if ($sscfilename != null) {
                    $this->deleteLocalFile($sscfilePath);
                }
                $file = $data['ssc_certificate'];
                $filename = date('YmdHi') . $file->getClientOriginalName();
                $file->move(public_path('uploads/ssc'), $filename);
                $data['ssc_certificate'] = $filename;
            }

            $signaturefilename = $userDetails->signature;
            $signaturefilePath = public_path('uploads/signatures/') . $signaturefilename;
            if (isset($data['signature'])) {
                if ($signaturefilename != null) {
                    $this->deleteLocalFile($signaturefilePath);
                }
                $file = $data['signature'];
                $filename = date('YmdHi') . $file->getClientOriginalName();
                $file->move(public_path('uploads/signatures'), $filename);
                $data['signature'] = $filename;
            }
            //unset($data['signature']);

            return $userDetails->update($data);
        } else {
            if (isset($data['image_url']) && $data['image_url'] !== null) {
                $file = $data['image_url'];
                $filename = date('YmdHi') . $file->getClientOriginalName();
                $file->move(public_path('uploads/images'), $filename);
                $data['image_url'] = $filename;
            }
            if (isset($data['hsc_certificate']) && $data['hsc_certificate'] !== null) {
                $file = $data['hsc_certificate'];
                $filename = date('YmdHi') . $file->getClientOriginalName();
                $file->move(public_path('uploads/hsc'), $filename);
                $data['hsc_certificate'] = $filename;
            }
            if (isset($data['ssc_certificate']) && $data['ssc_certificate'] !== null) {
                $file = $data['ssc_certificate'];
                $filename = date('YmdHi') . $file->getClientOriginalName();
                $file->move(public_path('uploads/ssc'), $filename);
                $data['ssc_certificate'] = $filename;
            }
            if (isset($data['signature']) && $data['signature'] !== null) {
                $file = $data['signature'];
                $filename = date('YmdHi') . $file->getClientOriginalName();
                $file->move(public_path('uploads/signatures'), $filename);
                $data['signature'] = $filename;
            }
            return UserDetail::create($data);
        }
    }

    public function search($column_name, $search_value)
    {
        $query = UserDetail::with('user.role', 'district', 'upazila', 'category', 'subCategory');

        if ($column_name == "name") {
            $words = explode(" ", $search_value);
            $f_name = $words[0] ?? '';
            $l_name = $words[1] ?? '';

            $query->whereHas('user', function ($query) use ($f_name, $l_name) {
                $query->where('fname', 'LIKE', '%' . $f_name . '%')
                    ->where('lname', 'LIKE', '%' . $l_name . '%');
            });
        } elseif ($column_name == "reg_id") {
            $query->whereHas('user', function ($query) use ($column_name, $search_value) {
                $query->where($column_name, 'LIKE', '%' . $search_value . '%');
            });
        } elseif ($column_name == "phone_number") {
            $query->whereHas('user', function ($query) use ($column_name, $search_value) {
                $query->where($column_name, 'LIKE', '%' . $search_value . '%');
            });
        } elseif ($column_name == "email") {
            $query->whereHas('user', function ($query) use ($column_name, $search_value) {
                $query->where($column_name, 'LIKE', '%' . $search_value . '%');
            });
        } elseif ($column_name == "upazila_id") {
            $query->where($column_name, 'LIKE', '%' . $search_value . '%');
        } elseif ($column_name == "district_id") {
            $query->where($column_name, 'LIKE', '%' . $search_value . '%');
        } elseif ($column_name == "division_id") {
            $districts = District::where('division_id', $search_value)->get();
            if ($districts->isNotEmpty()) {
                foreach ($districts as $key => $value) {
                    $query->orWhere('district_id', 'LIKE', '%' . $value->id . '%');
                }
            } else {
                return 0;
            }
        }

        return $query->get();
    }
}
