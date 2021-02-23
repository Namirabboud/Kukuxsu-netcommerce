<?php

namespace Kukuxsu\Netcommerce\Controllers;

use Exception;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

            $parameters = $this->get_ipay_parameters();

        }else if($type == 'BILL'){

            $parameters = [];

        }

        $config_path = $this->getConfigPath();

        return view('base::redirect')->with(compact('parameters'),$config_path);
    }



    public function paymentResponse(Request $request)
    {

        $transaction = $this->getTransactionInstanceByResponse_txtIndex(request('txtIndex'));

        if(!$transaction)
            dd('Transaction instance is empty return to documentation');


        if($transaction->completed)
            dd('transaction already submitted');

        DB::beginTransaction();
        try{

            $transaction = $this->responseUpdateTransaction($transaction);

            $this->responseAdditionalUpdates($transaction);

            DB::commit();

            $redirect_url = route('payment-mobile-response').'?success='.$transaction->success.'&message='.$transaction->resp_msg.' Orders Details: Order ID: '.$transaction->transaction_id.', Amount: '.number_format($transaction->amount).' '.$transaction->currency;

        }catch(Exception $e){

            DB::rollback();
            dd($e->getMessage());
        }



        return redirect($redirect_url);
    }
}
