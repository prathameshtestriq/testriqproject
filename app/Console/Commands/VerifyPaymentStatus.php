<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VerifyPaymentStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:verify-payment-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verify Payment Status Update';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        \Log::info("is log working");

        $url = 'https://info.payu.in/merchant/postservice?form=2';
        $Merchant_key = config('custom.merchant_key'); // set on custom file
        $SALT = config('custom.salt'); // set on custom file
        $command = 'verify_payment';
       
        $Sql = 'SELECT txnid,amount FROM booking_payment_details WHERE 1=1';
        $aResult = DB::select($Sql);
     
        if(!empty($aResult)){
            foreach($aResult as $res){
                $Transaction_id = $res->txnid;
                //dd($Transaction_id);
                $hashString = $Merchant_key . '|' . $command . '|' . $Transaction_id . '|' . $SALT;
                $hash = hash('sha512', $hashString);

                $postData = [
                    'key' => $Merchant_key,
                    'command' => 'verify_payment',
                    'hash' => $hash,
                    'var1' => $Transaction_id,
                ];

                // Make cURL request to PayUmoney API
                $ch = curl_init('https://info.payu.in/merchant/postservice?form=2');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

                $response = curl_exec($ch);
                curl_close($ch);

                $result = json_decode($response, true);
                //dd($result);

                $verify_payment_status = !empty($result['transaction_details'][$Transaction_id]['status']) ? $result['transaction_details'][$Transaction_id]['status'] : '';
                $payment_sub_status = !empty($result['transaction_details'][$Transaction_id]['unmappedstatus']) ? $result['transaction_details'][$Transaction_id]['unmappedstatus'] : '';
                $response_error_message = !empty($result['transaction_details'][$Transaction_id]['error_Message']) && isset($result['transaction_details'][$Transaction_id]['error_Message']) ? $result['transaction_details'][$Transaction_id]['error_Message'] : '';
            
                //dd($verify_payment_status,$payment_sub_status);
                 
                $up_sSQL = 'UPDATE booking_payment_details SET `verify_payment_status` =:verify_payment_status, `payment_sub_status` =:payment_sub_status, `response_error_message` =:response_error_message WHERE `txnid`=:Txnid ';
                DB::update($up_sSQL,array(
                    'verify_payment_status' => $verify_payment_status,
                    'payment_sub_status' => $payment_sub_status,
                    'response_error_message' => $response_error_message,
                    'Txnid' => $Transaction_id
                ));
                
                //--------- generate log
                $post_data = json_encode($postData);
                // dd($post_data);
                $Binding = array(
                                "txnid" => $Transaction_id,
                                "amount" => !empty($res->amount) ? $res->amount : 0,
                                "post_data" => $post_data,
                                "verify_payment_status" => $verify_payment_status
                            );
                $insert_SQL = "INSERT INTO cron_verify_payment_log(txnid,amount,post_data,verify_payment_status) VALUES(:txnid,:amount,:post_data,:verify_payment_status)";
                DB::insert($insert_SQL, $Binding);
                // die;
            }
        }

    }

  
}
