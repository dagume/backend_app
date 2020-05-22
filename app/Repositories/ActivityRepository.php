<?php

namespace App\Repositories;

use App\Activity;
use App\Repositories\BaseRepository;
use DB;

class ActivityRepository extends BaseRepository
{
    public function getModel()
    {
        return new Activity;
    }
    public function todayActivity($date){
        //Traemos las actividades que vencen hoy
        $reference = DB::select('select * from activities where date_end = ? and  is_act = false and is_added = false',[$date]);
        return $this->getObjects($reference);
    }
    public function betweenActivity($first_day, $last_day){
        //Traemos las actividades que vencen entre ciertas fechas
        $reference = DB::table('activities')
        ->where('is_added', false)
        ->where('is_act', false)
        ->whereBetween('date_end', [$first_day, $last_day])->get();
        return $this->getObjects($reference);
    }
    public function act_activity($project_id){
        //Traemos las actividades que son acta
        $acts = DB::select('select amount from activities where project_id = ? and is_act = true',[$project_id]);
        return $acts;
    }
    public function added_activity($project_id){
        //Traemos las actividades que son adicional
        $added = DB::select('select amount from activities where project_id = ? and is_added = true',[$project_id]);
        return $added;
    }

}
