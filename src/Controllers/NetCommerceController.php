<?php

namespace Kukuxsu\Netcommerce\Controllers;

use Exception;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Traits\NetcommerceTrait;

class NetCommerceController extends Controller
{

    use NetcommerceTrait;

    public function redirectPayment($id, Request $request)
    {
        //Actual Code
        $transaction = $this->getTransactionInstance($id);

        if(!$transaction)
            dd('Transaction instance is empty return to documentation');


        $type = $this->getPaymentType();

        if($type == 'IPAY'){

            $parameters = $this->getIpayParams($transaction);

        }else if($type == 'BILL'){

            $parameters = $this->getBillParameters($transaction);
        }

        $config_path = $this->getConfigPath();

        return view('base::redirect')->with(compact('parameters','config_path'));
    }



    public function paymentResponse(Request $request)
    {

        if(request('txtIndex'))
            $transaction = $this->getTransactionInstanceByResponse_txtIndex(request('txtIndex'));
        else if(request('txtScheduleID'))
            $transaction = $this->getTransactionInstanceByResponse_txtScheduleID(request('txtScheduleID'));

        if(!$transaction)
            dd('Transaction instance is empty return to documentation');


        if($transaction->completed)
            dd('transaction already submitted');

        DB::beginTransaction();
        try{

            $transaction = $this->responseUpdateTransaction($transaction);

            $this->responseAdditionalUpdates($transaction);

            DB::commit();

            if(request('err_msg'))
                $redirect_url = route('payment-mobile-response').'?success=0&message='.$transaction->err_msg;

            $redirect_url = route('payment-mobile-response').'?success='.$transaction->success.'&message='.$transaction->resp_msg.' Orders Details: Order ID: '.$transaction->transaction_id.', Amount: '.number_format($transaction->amount).' '.$transaction->currency;

        }catch(Exception $e){

            DB::rollback();
            dd($e->getMessage());
        }



        return redirect($redirect_url);
    }
}
