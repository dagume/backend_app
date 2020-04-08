<?php

namespace App\Repositories;

use App\PaymentAgreement;
use App\Repositories\BaseRepository;
use DB;

class PaymentAgreementRepository extends BaseRepository
{
    public function getModel()
    {
        return new PaymentAgreement;
    }
    public function todayPaymentAgreement($date){
        //Traemos los acuerdos de pagos que vencen hoy
        $data = DB::select('select * from payment_agreement where pay_date = ? and  state = false',[$date]);
        return $this->getObjects($data);
    }
    public function betweenPaymentAgreement($first_day, $last_day){
        //Traemos los acuerdos de pago que vencen hoy
        $data =  DB::select('select * from payment_agreement where pay_date between ? and ? and state = false',[$first_day, $last_day]);
        return $this->getObjects($data);
    }
    public function getPaymentAgreementsDate($project_id, $pay_date){
        //acuerdos de pago segun fecha dentro de un proyecto
        $data =  DB::select('select p_a.id, p_a.pay_date, p_a.amount, p_a.state, p_a.order_id, o.name from projects as p
        inner join orders as o on p.id = o.project_id
        inner join payment_agreement as p_a on o.id = p_a.order_id
        where o.project_id = ? and p_a.pay_date = ?',[$project_id, $pay_date]);
        return $data;
    }
    public function getprojectPayAgreMonth($project_id, $first_date, $end_date){
        //acuerdos de pago en rango de fechas dentro de un proyecto
        $data =  DB::select('select p_a.id, p_a.pay_date, p_a.amount, p_a.state, p_a.order_id, o.name from projects as p
        inner join orders as o on p.id = o.project_id
        inner join payment_agreement as p_a on o.id = p_a.order_id
        where o.project_id = ? and p_a.pay_date between ? and ? ',[$project_id, $first_date, $end_date]);
        return $data;
    }
}
