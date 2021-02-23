<?php

namespace App\Http\Traits;

trait NetcommerceTrait
{
    public function getTransactionInstance($id)
    {
        //return the transaction instance
        return null;
    }

    public function getTransactionInstanceByResponse_txtIndex($txtIndex)
    {
        /*
         * Ex: $transaction = Transaction::where('transaction_id', request('txtIndex'))->first();
         */

        return null;
    }

    public function getConfigPath()
    {
        /*
         * This if we want to differentiate multiple config parameters depends on
         * Some Parameter we have
         * example: Having two different payments each with different methods and merchant ids
         *
         */

        return 'services.netcommerce';
    }


    public function getPaymentType()
    {
        /*
         * This will return either IPAY or BILL
         * each has different params to be sent to NET_commerce
         */

        return 'IPAY';
    }


    public function getIpayParams($transaction)
    {

        $config_path = $this->getConfigPath();

        $user = $transaction->user;

        $parameters['payment_mode'] = config($config_path.'.mode');
        $parameters['txtAmount'] = $transaction->amount;
        $parameters['txtCurrency'] = $transaction->currency;
        $parameters['txtIndex'] = $transaction->transaction_id;
        $parameters['txtMerchNum'] = config($config_path.'.merchant_nb');
        $parameters['txthttp'] = route('netcommerce.payment.response');

        $signature = $parameters['txtAmount'].
            $parameters['txtCurrency'].
            $parameters['txtIndex'].
            $parameters['txtMerchNum'].
            $parameters['txthttp'].config('services.netcommerce.merchant_key');

        $parameters['signature'] = $secureHash=hash('sha256',$signature,false);

        $parameters['first_name'] = $user->first_name ? : 'not filled';
        $parameters['last_name'] = $user->last_name ? : 'not filled';
        $parameters['email'] = $user->email ? : 'namirabboud@gmail.com';
        $parameters['mobile'] = $user->phone_number ? : '009613123456';
        $parameters['address_line1'] = $user->phone_number ? : '009613123456';
        $parameters['city'] = 'City';
        $parameters['country'] = 'Lebanon';

        return $parameters;
    }


    public function responseUpdateTransaction($transaction)
    {
        //update the transaction
        $transaction->resp_code = request('RespVal');
        $transaction->resp_msg	= request('RespMsg');
        $transaction->auth_nb	= request('txtNumAut');
        $transaction->amount	= request('txtAmount');
        $transaction->sub_msg	= request('sub');
        $transaction->completed = true;
        $transaction->success	= request('RespVal') == 1 ? true : false;
        $transaction->save();

        return $transaction;
    }


    public function responseAdditionalUpdates($transaction)
    {
        //do something with the update transaction
    }
}
?>
