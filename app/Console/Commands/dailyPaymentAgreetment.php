<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Repositories\PaymentAgreementRepository;
use App\Events\PaymentAgreetment;
use App\Events\StatusLiked;

class dailyPaymentAgreetment extends Command
{
    protected $pay_agreRepo;


    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'daily:payment';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Commando para verificar cuales son los acuerdos de pago pendientes y proximos a vencer';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(PaymentAgreementRepository $payRepo){
        $this->pay_agreRepo = $payRepo;
        parent::__construct();
    }
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $day_week = date("w");

        if ($day_week == 0){
            $day_week = 7;
        }
        $first_day = '\''.date("Y-m-d", mktime(0, 0, 0, date("m")  , date("d")-$day_week+1, date("Y"))).'\'';
        $last_day = '\''.date("Y-m-d", mktime(0, 0, 0, date("m")  , date("d")+(7 - $day_week), date("Y"))).'\'';
        $paymentAgreements = $this->pay_agreRepo->betweenPaymentAgreement($first_day, $last_day);

        foreach ($paymentAgreements as $pay) {
            event(new PaymentAgreetment($pay));
        }
    }
}
