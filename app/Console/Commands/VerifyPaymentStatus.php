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
        // \Log::info("is log working");

        $url = 'https://info.payu.in/merchant/postservice?form=2';
        $Merchant_key = config('custom.merchant_key'); // set on custom file
        $SALT = config('custom.salt'); // set on custom file
        $command = 'verify_payment';

        $today = date('Y-m-d');
        $startTime = strtotime(date('Y-m-d 00:00:00', strtotime($today)));
        $endTime = strtotime(date('Y-m-d 23:59:59', strtotime($today)));
       
        $Sql = 'SELECT id,txnid,amount,created_datetime FROM booking_payment_details WHERE payment_status != "success" AND change_status_manual = 0 AND created_datetime BETWEEN '.$startTime.' AND '.$endTime.' ';
        $aResult = DB::select($Sql);
        // dd($aResult);
        $counter = 1;
        if(!empty($aResult)){
            foreach($aResult as $res){
                $Transaction_id = $res->txnid;
                $BookingPayId   = $res->id;

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
                $payment_mode = !empty($result['transaction_details'][$Transaction_id]['mode']) ? $result['transaction_details'][$Transaction_id]['mode'] : '';
                $mih_pay_id = !empty($result['transaction_details'][$Transaction_id]['mihpayid']) ? $result['transaction_details'][$Transaction_id]['mihpayid'] : '';
            
                //dd($verify_payment_status,$payment_sub_status);
                
                //------------- All transcation update entry 
                $up_sSQL = 'UPDATE booking_payment_details SET `verify_payment_status` =:verify_payment_status, `payment_sub_status` =:payment_sub_status, `response_error_message` =:response_error_message, `payment_mode` =:payment_mode WHERE `txnid`=:Txnid ';
                DB::update($up_sSQL,array(
                    'verify_payment_status' => $verify_payment_status,
                    'payment_sub_status' => $payment_sub_status,
                    'response_error_message' => $response_error_message,
                    'payment_mode' => $payment_mode,
                    'Txnid' => $Transaction_id
                ));
                 
                //------------------ new added for update payment_status 
                if($verify_payment_status == 'success'){

                    $up_sSQL1 = 'UPDATE booking_payment_details SET `payment_status` =:payment_status, `payment_mode` =:payment_mode WHERE `txnid`=:Txnid ';
                    DB::update($up_sSQL1,array(
                        'payment_status' => $verify_payment_status,
                        'payment_mode' => $payment_mode,
                        'Txnid' => $Transaction_id
                    ));
                    
                    //------------- event booking table update transaction_status
                    $up_sSQL2 = 'UPDATE event_booking SET `transaction_status` =:transaction_status WHERE `booking_pay_id`=:booking_pay_id ';
                    DB::update($up_sSQL2,array(
                        'transaction_status' => 1,
                        'booking_pay_id' => $BookingPayId
                    ));
                }

                //-------- update status for booking_payment_log
                $response_datetime = date('Y-m-d h:i:s');
                $up_sSQL = 'UPDATE booking_payment_log SET `mihpayid` =:mihpayid, `payment_status` =:payment_status, `response_datetime` =:response_datetime WHERE `txnid`=:Txnid ';
                DB::update(
                    $up_sSQL,
                    array(
                        'mihpayid' => $mih_pay_id,
                        'payment_status' => $verify_payment_status,
                        'response_datetime' => $response_datetime,
                        'Txnid' => $Transaction_id
                    )
                );
                
                //--------- generate log
                $post_data = json_encode($postData);
                // dd($post_data);
                $Binding = array(
                                "txnid" => $Transaction_id,
                                "amount" => !empty($res->amount) ? $res->amount : 0,
                                "post_data" => $post_data,
                                "count_no" => $counter,
                                "verify_payment_status" => $verify_payment_status
                            );
                $insert_SQL = "INSERT INTO cron_verify_payment_log(txnid,amount,post_data,count_no,verify_payment_status) VALUES(:txnid,:amount,:post_data,:count_no,:verify_payment_status)";
                DB::insert($insert_SQL, $Binding);
                // die;
                $counter++;
            }
        }

    }

  
}
