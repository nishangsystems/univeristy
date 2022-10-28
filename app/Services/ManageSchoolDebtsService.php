<?php
namespace App\Services;

use App\Models\SchoolUnits;

class ManageSchoolDebtsService {

    public function getSchoolUnits()
    {
        $base_units = SchoolUnits::where('parent_id', '>', 0)->get();
        $listing = [];
        $options = [];
        $separator = ' : ';
        foreach ($base_units as $key => $value) {
            if (array_key_exists($value->parent_id, $listing)) {
                $listing[$value->id] = $listing[$value->parent_id] . $separator . $value->name;
            } else {
                $listing[$value->id] = $value->name;
            }
            if ($base_units->where('id', '=', $value->parent_id)->count() > 0) {
                $listing[$value->id] = array_key_exists($value->parent_id, $listing) ?
                    $listing[$value->parent_id] . $separator . $value->name :
                    $base_units->where('id', '=', $value->parent_id)->pluck('name')[0] . $separator . $value->name;
            }
            foreach ($base_units->where('parent_id', '=', $value->id) as $keyi => $valuei) {
                $value->id > $valuei->id ?
                    $listing[$valuei->id] = $listing[$value->id] . $separator . $listing[$value->id] :
                    null;
            }
            if ($base_units->where('parent_id', '=', $value->id)->count() == 0) {
                $options[$value->id] = $listing[$value->id];
            }
        }
        return $options;
    }
}
