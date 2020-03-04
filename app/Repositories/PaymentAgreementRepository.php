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
        return $data;
    }
    public function betweenPaymentAgreement($first_day, $last_day){
        //Traemos los acuerdos de pago que vencen hoy
        $data =  DB::select('select * from payment_agreement where pay_date between ? and ? and state = false',[$first_day, $last_day]);
        return $data;
    }
}
