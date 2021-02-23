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

    public function getTransactionInstanceByResponse_txtScheduleID($txtScheduleID)
    {
        /*
         * Ex: $transaction = Transaction::where('transaction_id', request('txtScheduleID'))->first();
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
            $parameters['txthttp'].config($config_path.'.merchant_key');

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

    public function getBillParameters($transaction)
    {
        $config_path = $this->getConfigPath();

        $user = $transaction->user;

        $parameters['txtFirstName'] = $transaction->first_name;
        $parameters['txtLastName'] = $transaction->last_name;
        $parameters['txtEmail'] = $transaction->email;
        $parameters['txtPhone'] = $transaction->phone_number;
        $parameters['txtMobile'] = $transaction->phone_number;
        $parameters['txtCountry'] = $transaction->phone_number;
        $parameters['txtCountry'] = 'Lebanon';
        $parameters['txtCity'] = 'City';
        $parameters['txtAddress'] = $transaction->phone_number;
        $parameters['txtMerchNum'] = config($config_path.'.merchant_nb');


        /*
         * txtMerchReq Values
         * add_sch : register a new schedule
         * rac_sch: re-activate a schedule
         * dac_sch: de-activate a schedule
         * del_sch: delete a schedule
         * upd_sch: update a schedule
         * upd_cc: update a credit card
         *
         */

        $parameters['txtMerchReq'] = 'add_sch';



        $parameters['txthttp'] = route('netcommerce.payment.response');
        $parameters['txtScheduleID'] = $transaction->transaction_id;



        /*
         * txt_ScheduleStatus Values
         * 1 : schedule is active
         * 2 : schedule is not active
         *
         */

        $parameters['Flag_ScheduleStatus'] = 1;



        $parameters['txtRecurrentAmount'] = $transaction->amount;


        /*
         * txtRecurrentFreq Values
         * monthly, quarterly, yearly, bi-yearly
         *
         */

        $parameters['txtRecurrentFreq'] = $transaction->frequency;




        $parameters['txtCurrency'] = $transaction->currency;



        /*
         * Flag_IsPaymentEnds Values
         * 1: schedule has an end
         * 0: schedule is endless.
         *
         */

        $parameters['Flag_IsPaymentEnds'] = $transaction->currency;


        //$parameters['txtNumInstallments'] = $transaction->number_of_installments;

        $parameters['txtStartPaymentDate'] = $transaction->start_date;
        $parameters['txtEndPaymentDate'] = $transaction->end_date;

        $parameters['Flag_IsInstantPayment'] = 1;
        $parameters['txtInstantAmount'] = $transaction->amount;
        $parameters['txtInstantDescr'] = '';
        $parameters['Flag_BypassCardCheck'] = 1; //setting this to 0 will make a transaction with 1$ to check the card
        $parameters['next_payment_amount'] = $transaction->amount;
        //$parameters['next_payment_date'] = $date;
        //$parameters['txtNumAut'] = '';
        //$parameters['RespCode'] = '';



        if($parameters['txtMerchReq'] == 'add_sch'){
            //sha256(txtMerchNum&txtMerchReq&txtScheduleID&txthttp&Flag_ScheduleStatus&tx
            //tFirstName&txtLastName&txtEmail&txtPhone&txtMobile&txtCountry&txtCity&txtAdd
            //ress&txtRecurrentAmount&txtRecurrentFreq&txtCurrency&Flag_IsPaymentEnds&txtN
            //umInstallments&txtStartPaymentDate&txtEndPaymentDate&Flag_IsInstantPayment&t
            //xtInstantAmount&txtInstantDescr&sha256_key)

            $signature = $parameters['txtMerchNum'].
                $parameters['txtMerchReq'].$parameters['txtScheduleID'].$parameters['txthttp'].
                $parameters['Flag_ScheduleStatus'].$parameters['txtFirstName'].$parameters['txtLastName'].
                $parameters['txtEmail'].$parameters['txtPhone'].$parameters['txtMobile'].
                $parameters['txtCountry'].$parameters['txtCity'].$parameters['txtAddress'].
                $parameters['txtRecurrentAmount'].$parameters['txtRecurrentFreq'].$parameters['txtCurrency'].
                $parameters['Flag_IsPaymentEnds'].$parameters['txtNumInstallments'].$parameters['txtStartPaymentDate'].
                $parameters['txtEndPaymentDate'].$parameters['Flag_IsInstantPayment'].$parameters['txtInstantAmount'].
                $parameters['txtInstantDescr'].config($config_path.'.merchant_key');
        }


        $parameters['signature'] = $secureHash=hash('sha256',$signature,false);

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
        $transaction->success	= request('RespCode') == '00' ? true : false;
        $transaction->save();

        return $transaction;
    }


    public function responseAdditionalUpdates($transaction)
    {
        //do something with the update transaction
    }
}
?>
