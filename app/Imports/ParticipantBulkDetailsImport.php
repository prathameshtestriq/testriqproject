<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ParticipantBulkDetailsImport implements ToCollection, WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    protected $userData;
    public $returnData;

    public function __construct($userData,$data=true)
    {
        $this->userData = $userData;
        $this->returnData['data'] = $data;
    }

    public function collection(Collection $collection)
    {
       
        foreach($collection as  $row){
            if($row->filter()->isNotEmpty()){
                $validator = Validator::make($row->toArray(), [
                    'ticket_name' => 'required',
                ]);
                if($validator->fails()){ 
                    return $this->returnData['data'] = $validator->errors()->first();
                }
            }
        }

        if($this->returnData['data'] === true){
            
            $this->returnData['DataFound'] = array();
            $this->returnData['emailAddressNotFound']  = array();
            $this->returnData['BookPayId'] = 0;
            $userId     =  $this->userData['userId'];
            $event_id   =  (int)$this->userData['event_id'];
            $group_name =  $this->userData['group_name'];
            // dd($group_name);

            $i = 1; $j = 1;
            // dd($userId,$event_id);
            
            $SQL = "SELECT id,question_label,question_form_name FROM event_form_question WHERE event_id = ".$event_id." ORDER BY sort_order ASC";
            $aEventFormResult = DB::select($SQL, array());
            $question_form_name_array = !empty($aEventFormResult) ? array_column($aEventFormResult, "question_form_name") : [];
            // dd($question_form_name_array);

            // dd($collection);number_format
            $FinalFormQuestions = $FinalHeaderData = $selected_ticket_array = $ticket_ids_array = []; $Final_ticket_amount = 0; 
            $total_extra_amount = $ticket_amt = 0;
            $excel_ticket_price_array = $excel_single_ticket_price = [];

            foreach ($collection as $key=>$row)
            {
                //echo $row['email_address'].'<br>'
                if($row->filter()->isNotEmpty() && $row != null){

                   if($row['email_address'] != ''){
                    
                    //------------------------ get header key & value pair
                    $headers = $row->keys();
                    $values = $row->values();
                    // Combine headers and values into key-value pairs
                    $HeaderData = [];
                    foreach ($headers as $index => $header) {
                        $HeaderData[$header] = $values[$index];
                    }

                    $FinalHeaderData = array_filter($HeaderData, function($value) {
                        return !is_null($value);
                    });
                    // dd($FinalHeaderData);
                    //--------------------------

                    $exSheetTicketName  = isset($row['ticket_name']) ? $row['ticket_name']: '';
                    $exSheetTicketPrice = isset($row['ticket_price']) ? $row['ticket_price']: '';

                    $sSQL = 'SELECT id,ticket_name,player_of_fee,player_of_gateway_fee FROM event_tickets WHERE event_id =:event_id AND LOWER(ticket_name) =:ticket_name';
                    $AllTickets = DB::select($sSQL, array('event_id' => $event_id, 'ticket_name' => strtolower($exSheetTicketName)));
                    // dd($AllTickets);

                    if(!empty($AllTickets)){
                        foreach ($AllTickets as $ticket) {
                            $sSQL = 'SELECT id,event_id,general_form_id,question_label,question_form_type,question_form_name,question_form_option,child_question_ids,user_field_mapping,question_status,parent_question_id,apply_ticket,ticket_details
                                    FROM event_form_question 
                                    WHERE event_id = :event_id 
                                    AND FIND_IN_SET(:ticket_id, ticket_details)
                                    AND question_status = 1 
                                    ORDER BY sort_order,parent_question_id';

                            $FormQuestions = DB::select($sSQL, [
                                'event_id'  => $event_id,
                                'ticket_id' => $ticket->id
                            ]);
                           // dd($FormQuestions,$FinalHeaderData);
                            
                            if(!empty($FormQuestions))
                                foreach ($FormQuestions as $value) {
                                    //---------- check type [select, radio] $value->question_form_type == "select" || 
                                    if (!empty($value->question_form_option) && ($value->question_form_type == "select" || $value->question_form_type == "radio") ) {
                                
                                        $question_form_name = str_replace("_?", "", strtolower(trim($value->question_form_name)));
                                        $question_form_name = str_replace("?", "", $question_form_name);
                                        $question_form_name = str_replace("-", "_", $question_form_name);
                                        // dd($question_form_name);
                                        if(isset($FinalHeaderData[$question_form_name])){
                                            $selectedValue = (isset($FinalHeaderData[$question_form_name])) ? $FinalHeaderData[$question_form_name] : "";
                                        }else if($value->question_form_name == "sub_question"){
                                            $question_label_name = str_replace(" ", "_", strtolower(trim($value->question_label)));
                                            $selectedValue = (isset($FinalHeaderData[$question_label_name])) ? $FinalHeaderData[$question_label_name] : "";
                                        }
                                        else{
                                            $selectedValue = (isset($FinalHeaderData[$value->user_field_mapping])) ? $FinalHeaderData[$value->user_field_mapping] : "";
                                        }
                                    
                                        $jsonString = $value->question_form_option;
                                        $optionArray = json_decode($jsonString, true);
                                         
                                        // Get the id if the label exists
                                        if(!empty($selectedValue)){
                                            $index = array_search($selectedValue, array_column($optionArray, "label"));
                                            $option_id = $index !== false ? $optionArray[$index]['id'] : null;
                                            $value->ActualValue = (string)$option_id;
                                        }else
                                           $value->ActualValue = '';
                                           // dd($value->ActualValue);
                                    }else if($value->question_form_type == "countries"){
                                        $selectedCountry = (isset($FinalHeaderData[$value->user_field_mapping])) ? $FinalHeaderData[$value->user_field_mapping] : "";
                                        $sql = "SELECT id,name AS label FROM countries WHERE flag=1 AND name = '".$selectedCountry."' order by name asc";
                                        $countries = DB::select($sql);
                                        
                                        if(!empty($countries)){
                                           $value->ActualValue = (string)$countries[0]->id;
                                        }else
                                           $value->ActualValue = '';  

                                    }else if($value->question_form_type == "states"){
                                        $selectedState = (isset($FinalHeaderData[$value->user_field_mapping])) ? $FinalHeaderData[$value->user_field_mapping] : "";
                                        $sql = "SELECT id,name FROM states WHERE country_id = 101 AND name = '".$selectedState."' order by name asc";
                                        $states = DB::select($sql);
                                        
                                        if(!empty($states)){
                                           $value->ActualValue = (string)$states[0]->id;
                                        }else
                                           $value->ActualValue = '';  
                                        
                                    }else if($value->question_form_type == "cities"){
                                        $selectedCity = (isset($FinalHeaderData[$value->user_field_mapping])) ? $FinalHeaderData[$value->user_field_mapping] : "";
                                        $sql = "SELECT id,name FROM cities WHERE country_id = 101 AND name = '".$selectedCity."' order by name asc";
                                        $cities = DB::select($sql);
                                        
                                        if(!empty($cities)){
                                           $value->ActualValue = (string)$cities[0]->id;
                                        }else
                                           $value->ActualValue = '';  
                                        
                                    }else if($value->question_form_type == "date"){
                                        
                                        if(isset($FinalHeaderData[$value->question_form_name]) && !empty($FinalHeaderData[$value->question_form_name])){
                                            $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject( (isset($FinalHeaderData[$value->question_form_name])) ? $FinalHeaderData[$value->question_form_name] : "");
                                            $value->ActualValue = $date->format('Y-m-d');
                                        }else{
                                            $value->ActualValue = ''; 
                                        }
                                        
                                    }else if($value->question_form_type == "amount"){
                                        $question_label_name = str_replace(" ", "_", strtolower(trim($value->question_label)));
                                        $selectedValue = (isset($FinalHeaderData[$question_label_name])) ? $FinalHeaderData[$question_label_name] : "";
                                        $value->ActualValue = !empty($selectedValue) ? $selectedValue : 0;
                                    }
                                    else if($value->question_form_type == "text"){
                                        $question_label_name = str_replace("_/_", "_", strtolower(trim($value->question_form_name)));   
                                        $question_label_name = str_replace("(", "", strtolower(trim($question_label_name)));   
                                        $question_label_name = str_replace(")", "", strtolower(trim($question_label_name)));   
                                        $selectedValue = (isset($FinalHeaderData[$question_label_name])) ? $FinalHeaderData[$question_label_name] : "";
                                        $value->ActualValue = !empty($selectedValue) ? $selectedValue : 0;
                                    }
                                    else{  // [text]
                                        $value->ActualValue = (isset($FinalHeaderData[$value->question_form_name])) ? $FinalHeaderData[$value->question_form_name] : "";
                                    }
                                    
                                    
                                    $value->Error = "";
                                    $value->TicketId = isset($ticket->id) ? $ticket->id : 0;
                                    
                                    //---------------- new extra amount total
                                    if($value->question_form_type == "amount"){
                                        if(!empty($value->ActualValue))
                                            $total_extra_amount += $value->ActualValue;
                                    }
                                }
                            
                           // / for ($i = 0; $i < 1; $i++) {
                                if (count($FormQuestions) > 0)
                                    $FinalFormQuestions[$ticket->id][] = ["form_array" => $FormQuestions, "ticket_amount" => !empty($exSheetTicketPrice) ? $exSheetTicketPrice : 0, "player_of_fee" => $ticket->player_of_fee , "player_of_gateway_fee" => $ticket->player_of_gateway_fee] ;
                            // }
                            $ticket_ids_array[] = $ticket->id;  

                            //------------- 
                            if(isset($selected_ticket_array[$ticket->id])){
                                $selected_ticket_array[$ticket->id] += 1;
                            }else{
                                $selected_ticket_array[$ticket->id] = 1;
                            }
                           
                            //----- new added
                            if(isset($excel_ticket_price_array[$ticket->id])){
                                $excel_ticket_price_array[$ticket->id] += $exSheetTicketPrice;
                            }else{
                                $excel_ticket_price_array[$ticket->id] = $exSheetTicketPrice;
                            }

                            // $excel_single_ticket_price[] = ["ticket_id" => $ticket->id, "ticket_amount" => !empty($exSheetTicketPrice) ? $exSheetTicketPrice : 0 ];
                        }
                        $this->returnData['DataFound'] = $i;
                        $i++;
                    }else{
                        $this->returnData['emailAddressNotFound'] = $j;
                        $j++;
                    }
                    
                  }else{
                 
                    $this->returnData['emailAddressNotFound'] = $j;
                    $j++;
                  }
                }
            } 
           // dd($FinalHeaderData,$FormQuestions);
            // dd($FinalHeaderData,$FinalFormQuestions);
          
            //--------------------- data insert --------------------

            $Amount = !empty($request->amount) ? $request->amount : '0.00';
            $Datetime = time();
            $request_datetime = date('Y-m-d H:i:s');

            $Sql = 'SELECT name,collect_gst,prices_taxes_status FROM events WHERE active = 1 and id = ' . $event_id . ' ';
            $event_Result = DB::select($Sql);

            $Sql = 'SELECT id,firstname,lastname,email,mobile FROM users WHERE is_active = 1 and id = ' . $userId . ' ';
            $aResult = DB::select($Sql);

            $FirstName = !empty($aResult[0]->firstname) ? $aResult[0]->firstname : '';
            $LastName = !empty($aResult[0]->lastname) ? $aResult[0]->lastname : '';
            $Email = !empty($aResult[0]->email) ? $aResult[0]->email : '';
            $PhoneNo = !empty($aResult[0]->mobile) ? $aResult[0]->mobile : '';
            $ProductInfo = !empty($event_Result) ? $event_Result[0]->name : '';
             
            $Merchant_key = config('custom.merchant_key'); // set on custom file
            $SALT = config('custom.salt'); // set on custom file

            $Sql = 'SELECT counter FROM booking_payment_details WHERE 1=1 order by id desc limit 1';
            $aResult = DB::select($Sql);

            $last_count = !empty($aResult) && !empty($aResult[0]->counter) ? $aResult[0]->counter + 1 : 1;
            $txnid = !empty($last_count) ? 'YTCR-' . date('dmy') . '-' . $last_count : 'YTCR-' . date('dmy') . '-1';
            
            $hashString = $Merchant_key . '|' . $txnid . '|' . $Amount . '|' . $ProductInfo . '|' . $FirstName . '|' . $Email . '|||||||||||' . $SALT;
            $hash = hash('sha512', $hashString);

            //------------------- event ticket array create
            $now = strtotime("now");  // AND ticket_sale_start_date <= :now_start AND ticket_sale_end_date >= :now_end
            $sSQL = 'SELECT * FROM event_tickets WHERE event_id = :event_id AND active = 1 AND is_deleted = 0';
            if(!empty($ticket_ids_array))
              $sSQL .=' AND id IN ('.implode(",", array_unique($ticket_ids_array)).')';
            
            $sSQL .= ' order by sort_order asc';
            $AllTickets = DB::select($sSQL, array('event_id' => $event_id));  // , 'now_start' => $now, 'now_end' => $now
            // dd($sSQL);

            $ticket_calculation_details = [];
            $limit_exceed_flag = $limit_exceed = 0;
            // dd($AllTickets);
           
            if(!empty($AllTickets)){
                foreach ($AllTickets as $value) {
                    if(isset($selected_ticket_array[$value->id]))
                        $value->count = $selected_ticket_array[$value->id];
                    else
                        $value->count = 0;
                   
                    $value->Error = "";

                    $value->display_ticket_name = !empty($value->ticket_name) ? (strlen($value->ticket_name) > 40 ? ucwords(substr($value->ticket_name, 0, 80)) . "..." : ucwords($value->ticket_name)) : "";
                    $sql = "SELECT COUNT(a.id) AS TotalBookedTickets
                    FROM attendee_booking_details AS a 
                    LEFT JOIN booking_details AS b ON b.id=a.booking_details_id
                    LEFT JOIN event_booking AS e ON b.booking_id = e.id
                    WHERE b.event_id =:event_id AND b.ticket_id=:ticket_id AND e.transaction_status IN (1,3)";

                    $TotalTickets = DB::select($sql, array("event_id" => $event_id, "ticket_id" => $value->id));

                    $value->TotalBookedTickets = ((sizeof($TotalTickets) > 0) && (isset($TotalTickets[0]->TotalBookedTickets))) ? $TotalTickets[0]->TotalBookedTickets : 0;

                    $value->RemainingTickets = $RemainingTickets = 0;
                    if ($value->total_quantity > $value->TotalBookedTickets) {
                        $RemainingTickets = $value->total_quantity - $value->TotalBookedTickets;
                        if ($RemainingTickets <= 10) {
                            $value->RemainingTickets = $RemainingTickets;
                        }
                    }
                    $value->show_early_bird = 0;

                    $value->discount_ticket_price = 0;
                    $value->total_discount = 0;
                    $loc_total_discount = 0;
                    if ($value->early_bird == 1 && $value->TotalBookedTickets <= $value->no_of_tickets && $value->start_time <= $now && $value->end_time >= $now) {
                        $value->show_early_bird = 1;
                        $value->strike_out_price = ($value->early_bird == 1) ? $value->ticket_price : 0;

                        if ($value->discount == 1) { //percentage
                            $value->total_discount = ($value->ticket_price * ($value->discount_value / 100));
                            // $value->total_discount = !empty($loc_total_discount) ? number_format($loc_total_discount,2) : '0.00';
                            $value->discount_ticket_price = $value->ticket_price - $value->total_discount;
                        } else if ($value->discount == 2) { //amount
                            $value->total_discount = $value->discount_value; // !empty($value->discount_value) ? number_format($loc_total_discount,2)  : '0.00';
                            $value->discount_ticket_price = $value->ticket_price - $value->discount_value;
                        }
                    }

                    $current_time = time();
                    $two_days_later = $current_time + (2 * 24 * 60 * 60);
                    if (!empty($value->ticket_sale_end_date) && $value->ticket_sale_end_date <= $two_days_later) {
                        $value->ticket_sale_end_date = date("d M Y H:i A", $value->ticket_sale_end_date);
                    } else {
                        $value->ticket_sale_end_date = "";
                    }

                    $ticket_calculation_details = $value->ticket_calculation_details = !empty($value->ticket_calculation_details) ? json_decode($value->ticket_calculation_details) : [];

                    if(!empty($ticket_calculation_details)){
                        $value->OrgGstPercentage = $ticket_calculation_details->convenience_fees_gst_percentage;
                        $value->TicketYtcrBasePrice = $ticket_calculation_details->convenience_fee_base;
                        $value->YTCR_FEE_PERCENT = config('custom.ytcr_fee_percent');
                        $value->PLATFORM_FEE_PERCENT = $ticket_calculation_details->platform_fees_5_each;
                        $value->PAYMENT_GATEWAY_FEE_PERCENT = $ticket_calculation_details->gst_on_platform_fees;
                        $value->PAYMENT_GATEWAY_GST_PERCENT = $ticket_calculation_details->payment_gateway_gst;

                        $value->total_buyer = $ticket_calculation_details->total_buyer;
                        $value->to_organiser = $ticket_calculation_details->to_organiser;
                        $value->registration_18_percent_GST = $ticket_calculation_details->registration_18_percent_GST;
                    }

                    //----------------------------
                    if(!empty($overall_limit) && !empty($value->min_booking)){
                        $limit_exceed = ($total_booking_registration + (int)$value->min_booking);
                        if($limit_exceed > $overall_limit){
                            $limit_exceed_flag = 1;
                        }else{
                            $limit_exceed_flag = 0;
                        }
                    }else{ $limit_exceed_flag = 0; }

                    $value->limit_exceed_count = $limit_exceed;
                    $value->limit_exceed_flag  = $limit_exceed_flag;
                }
            }
            
            //-------------------- create cart details array
            $Sql1 = 'SELECT id,registration_amount,convenience_fee,platform_fee,payment_gateway_fee FROM race_category_charges WHERE event_id =:event_id';
            $aCatChargesResult = DB::select($Sql1, array('event_id' => $event_id));
            // dd($aCatChargesResult);
            $ConvenienceFeeBase = $NewPlatformFee = $NewPaymentGatewayFee = $Convenience_Fee_Amount = $GstPercentage = $BasePriceGst = $Basic_Amount_Gst = $Convenience_Fees_Gst_Percentage = $GST_On_Platform_Fees = $Payment_Gateway_Gst = 0;
            $GST_On_Convenience_Fees = $Total_Convenience_Fees = $GST_On_Platform_Fees_Amount = $Total_Platform_Fees = $Net_Registration_Amount = $Payment_Gateway_Buyer = $Payment_Gateway_gst_amount = $Total_Payment_Gateway = $BuyerPayment = $totalPlatformFee = $totalTaxes = 0;

            if ($event_Result[0]->collect_gst === 1 && $event_Result[0]->prices_taxes_status === 2) {  // get for organization page
                $GstPercentage = 18;
            }else{
                $GstPercentage = 0;
            }

            $Convenience_Fees_Gst_Percentage = 18;
            $GST_On_Platform_Fees = 18;
            $Payment_Gateway_Gst = 18;
                 // dd($AllTickets);  
            $cart_details_array = []; 
            $Final_ticket_count = 0;
            $Extra_Amount_Payment_Gateway = $Extra_Amount_Payment_Gateway_Gst = $additional_amount = $Final_Extra_Amount = 0;

            if(!empty($AllTickets)){
                foreach($AllTickets as $res){
                    
                    $res->single_ticket_price = $res->ticket_price;

                    if(isset($excel_ticket_price_array[$res->id]))
                        $res->ticket_price = $excel_ticket_price_array[$res->id];
                    else
                        $res->ticket_price = $res->ticket_price;

                    //$res->ticket_price = ($res->ticket_price * $res->count);
                    // dd($res->ticket_price);
                                        
                    if(!empty($aCatChargesResult)){
                        for ($i=0; $i < count($aCatChargesResult); $i++) { 
                            // dd($aCatChargesResult[$i]->convenience_fee);
                            if ($aCatChargesResult[$i]->registration_amount >= floatval($res->ticket_price)){
                                //console.log($aCatChargesResultDetails[i]['convenience_fee']);
                                $ConvenienceFeeBase = ($aCatChargesResult[$i]->convenience_fee);
                                $NewPlatformFee = ($aCatChargesResult[$i]->platform_fee);       // 5 Rs
                                $NewPaymentGatewayFee = ($aCatChargesResult[$i]->payment_gateway_fee); // 1.85 %
                                break;
                            }else if($i == (count($aCatChargesResult)-1) && $aCatChargesResult[$i]->registration_amount <= floatval($res->ticket_price)){
                                //console.log($aCatChargesResult[i]['convenience_fee']);
                                $ConvenienceFeeBase = ($aCatChargesResult[$i]->convenience_fee);
                                $NewPlatformFee = ($aCatChargesResult[$i]->platform_fee);       // 5 Rs
                                $NewPaymentGatewayFee = ($aCatChargesResult[$i]->payment_gateway_fee); // 1.85 %
                                break;
                            }
                        }
                    }
                     // dd($aCatChargesResult,$res->ticket_price,$ConvenienceFeeBase,$NewPlatformFee,$NewPaymentGatewayFee);
                    
                    $NewPlatformFee = ($NewPlatformFee * $res->count);

                    if ($event_Result[0]->collect_gst == 1 && $event_Result[0]->prices_taxes_status == 2) {
                        $BasePriceGst = floatval($res->ticket_price) != 0 ? floatval($res->ticket_price) * ($GstPercentage / 100) : 0; // GST %
                        $Basic_Amount_Gst = (floatval($BasePriceGst) + floatval($res->ticket_price));
                    } else {
                        $BasePriceGst = '0.00';
                        $Basic_Amount_Gst = floatval($res->ticket_price); // registration amt
                    }
                   
                   
                    if((int)$ConvenienceFeeBase == 30 || (int)$ConvenienceFeeBase == 40 || (int)$ConvenienceFeeBase == 10){
                        //console.log('ss');
                        $Convenience_Fee_Amount = (int)$ConvenienceFeeBase;
                    }else{
                        $Convenience_Fee_Amount = $Basic_Amount_Gst * ((int)$ConvenienceFeeBase / 100);  
                    }

                    $GST_On_Convenience_Fees = floatval($Convenience_Fee_Amount) * ($Convenience_Fees_Gst_Percentage / 100); // GST 18%
                    $Total_Convenience_Fees = (floatval($Convenience_Fee_Amount) + $GST_On_Convenience_Fees);
                    $GST_On_Platform_Fees_Amount = $NewPlatformFee * ($GST_On_Platform_Fees / 100); // GST 18%
                    $Total_Platform_Fees = (floatval($NewPlatformFee) + floatval($GST_On_Platform_Fees_Amount));
                    $Net_Registration_Amount = (floatval($Basic_Amount_Gst) + floatval($Total_Convenience_Fees) + floatval($Total_Platform_Fees));
                   // dd($Convenience_Fee_Amount,$GST_On_Convenience_Fees);
                    // dd($res->player_of_fee,$res->player_of_gateway_fee);
                    if((int)$res->player_of_fee == 1 && (int)$res->player_of_gateway_fee == 1) {  //Organiser + Organiser
        
                        $Payment_Gateway_Buyer = $Basic_Amount_Gst * ($NewPaymentGatewayFee / 100); // 1.85%
                        $Payment_Gateway_gst_amount = $Payment_Gateway_Buyer * ($Payment_Gateway_Gst / 100); //18%
                        $Total_Payment_Gateway = (floatval($Payment_Gateway_Buyer) + floatval($Payment_Gateway_gst_amount));
                        $BuyerPayment = $Basic_Amount_Gst;  // yes
                        $totalPlatformFee = 0;
                        $totalTaxes = floatval($BasePriceGst);
                        
                        //------------- additional amt calculation 
                        $additional_amount = !empty($total_extra_amount) ? $total_extra_amount : 0; 

                    }else if((int)$res->player_of_fee == 2 && (int)$res->player_of_gateway_fee == 2) {  // Participant + Participant
                        
                        $Payment_Gateway_Buyer = $Net_Registration_Amount * ($NewPaymentGatewayFee / 100); // 1.85%
                        $Payment_Gateway_gst_amount = $Payment_Gateway_Buyer * ($Payment_Gateway_Gst / 100); //18%
                        $Total_Payment_Gateway = (floatval($Payment_Gateway_Buyer) + floatval($Payment_Gateway_gst_amount));
                        $BuyerPayment = (floatval($Total_Payment_Gateway) + floatval($Net_Registration_Amount));
                        // dd($Convenience_Fee_Amount, $NewPlatformFee, $Payment_Gateway_Buyer);
                        $totalPlatformFee = floatval($Convenience_Fee_Amount) + floatval($NewPlatformFee) + floatval($Payment_Gateway_Buyer);
                        $totalTaxes = floatval($BasePriceGst) + floatval($GST_On_Convenience_Fees) + floatval($GST_On_Platform_Fees_Amount) + floatval($Payment_Gateway_gst_amount);
                        // dd($Convenience_Fee_Amount,$NewPlatformFee,$Payment_Gateway_Buyer);
                        
                        //--------------- additional amt calculation
                        if(!empty($total_extra_amount)){
                            $additional_amount = !empty($total_extra_amount) ? $total_extra_amount : 0; 
                            $Extra_Amount_Payment_Gateway = $total_extra_amount * ($NewPaymentGatewayFee / 100); // 1.85%
                            $Extra_Amount_Payment_Gateway_Gst = $Extra_Amount_Payment_Gateway * ($Payment_Gateway_Gst / 100); //18%
                        }

                    }else if((int)$res->player_of_fee == 1 && (int)$res->player_of_gateway_fee == 2) { // Organiser + Participant
                        
                        $Payment_Gateway_Buyer = $Basic_Amount_Gst * ($NewPaymentGatewayFee / 100); // 1.85%
                        $Payment_Gateway_gst_amount = $Payment_Gateway_Buyer * ($Payment_Gateway_Gst / 100); //18%
                        $Total_Payment_Gateway = (floatval($Payment_Gateway_Buyer) + floatval($Payment_Gateway_gst_amount));
                        $BuyerPayment = (floatval($Basic_Amount_Gst) + floatval($Total_Payment_Gateway));
                        $totalPlatformFee = floatval($Payment_Gateway_Buyer);
                        $totalTaxes = floatval($BasePriceGst) + floatval($Payment_Gateway_gst_amount);

                        //------------- additional amt calculation
                        $additional_amount = !empty($total_extra_amount) ? $total_extra_amount : 0; 

                    }else if((int)$res->player_of_fee == 2 && (int)$res->player_of_gateway_fee == 1) { // Participant + Organiser
                        
                        //--------------- additional amt calculation
                        if(!empty($total_extra_amount)){
                            $additional_amount = !empty($total_extra_amount) ? $total_extra_amount : 0; 
                            $Payment_Gateway_Buyer = ($additional_amount + $Net_Registration_Amount) * ($NewPaymentGatewayFee / 100); // 1.85%
                        }else{
                            $Payment_Gateway_Buyer = $Net_Registration_Amount * ($NewPaymentGatewayFee / 100); // 1.85%
                        }

                        $Payment_Gateway_gst_amount = $Payment_Gateway_Buyer * ($Payment_Gateway_Gst / 100); //18%
                        $Total_Payment_Gateway = (floatval($Payment_Gateway_Buyer) + floatval($Payment_Gateway_gst_amount));
                        $BuyerPayment = (floatval($Basic_Amount_Gst) + floatval($Total_Convenience_Fees) + floatval($Total_Platform_Fees) );
                        $totalPlatformFee = floatval($Convenience_Fee_Amount) + floatval($NewPlatformFee);
                        $totalTaxes = floatval($BasePriceGst) + floatval($GST_On_Convenience_Fees) + floatval($GST_On_Platform_Fees_Amount);
                    }

                    $res->YtcrFee     = $res->ticket_price;
                    $res->collect_gst = !empty($event_Result) ? $event_Result[0]->collect_gst : 0;
                    $res->Main_Price  = number_format($res->ticket_price,2);
                    $res->OrgPayment  = '0.00';
                    $res->YtcrAmount  = number_format(($res->ticket_price * ($res->YTCR_FEE_PERCENT /100)),2);
                    $res->Extra_Amount  = number_format($additional_amount,2);
                    $res->Total_Taxes   = number_format($totalTaxes,2);
                    $res->BuyerPayment  = number_format($BuyerPayment,2);
                    $res->Platform_Fee  = number_format($NewPlatformFee,2);
                    $res->Convenience_Fee  = number_format($ConvenienceFeeBase,2);
                    $res->RegistrationFee  = number_format($BasePriceGst,2);
                    $res->ticket_discount  = 0;
                    $res->YtcrGstPercentage  = $res->YTCR_FEE_PERCENT;
                    $res->ticket_show_price  = 0;
                    $res->Total_Platform_Fee  = number_format($totalPlatformFee,2);
                    $res->single_ticket_flag  = 1;
                    $res->ExcPriceTaxesStatus  = $event_Result[0]->prices_taxes_status;
                    $res->Payment_Gateway_Fee  = $NewPaymentGatewayFee;
                    $res->Platform_Fee_GST_18  = number_format($GST_On_Platform_Fees_Amount,2);
                    $res->PLATFORM_FEE_PERCENT  = $res->PLATFORM_FEE_PERCENT;
                    $res->PaymentGatewayAmount  = 0;
                    $res->Registration_Fee_GST  = number_format($BasePriceGst,2);
                    $res->PaymentGatewayWithGst  = 0;
                    $res->Convenience_Fee_GST_18  = number_format($GST_On_Convenience_Fees,2);
                    $res->Payment_Gateway_GST_18  = number_format($Payment_Gateway_gst_amount,2);
                    $res->Payment_Gateway_Charges  = number_format($Payment_Gateway_Buyer,2);
                    $res->PaymentGatewayPercentage  = $res->PAYMENT_GATEWAY_FEE_PERCENT;
                    $res->RegistrationGstPercentage  = $res->OrgGstPercentage;
                    $res->PaymentGatewayGstPercentage  = $res->PAYMENT_GATEWAY_GST_PERCENT;
                    $res->Extra_Amount_Payment_Gateway  = number_format($Extra_Amount_Payment_Gateway,2);
                    $res->BuyerAmtWithoutPaymentGateway  = '0.00';
                    $res->Extra_Amount_Payment_Gateway_Gst  = number_format($Extra_Amount_Payment_Gateway_Gst,2);

                    $cart_details_array[] = $res;

                    //------------- final ticket value
                  
                    $Final_ticket_count  += $value->count;

                    if(!empty($res->ticket_price)){
                        $Final_ticket_amount += $BuyerPayment;
                        $Final_Extra_Amount  += ($additional_amount+$Extra_Amount_Payment_Gateway+$Extra_Amount_Payment_Gateway_Gst);
                    }else{
                        $Final_Extra_Amount = $Final_ticket_amount = 0;
                    }

                }
            }
            // dd($Final_ticket_amount,$Final_Extra_Amount);

            //----------- Ticket grand total
            // $Final_calculated_ticket_amount = (($Final_ticket_amount*$Final_ticket_count)+$Final_Extra_Amount);
            $Final_calculated_ticket_amount = ($Final_ticket_amount+$Final_Extra_Amount);
             // dd($Final_calculated_ticket_amount);

            //----------------------------- insert data for (booking_payment_details) table
            $Bindings = array(
                "event_id" => $event_id,
                "txnid" => $txnid,
                "firstname" => $FirstName,
                "lastname" => $LastName,
                "email" => $Email,
                "phone_no" => $PhoneNo,
                "productinfo" => $ProductInfo,
                "amount" => !empty($Final_calculated_ticket_amount) ? $Final_calculated_ticket_amount : 0,
                "merchant_key" => $Merchant_key,
                "hash" => $hash,
                "created_by" => $userId,
                "created_datetime" => $Datetime,
                "counter" => $last_count,
                "payment_status" => !empty($Final_calculated_ticket_amount) ? 'success' : 'free',
                "bulk_upload_flag" => 1,
                "bulk_upload_group_name" => $group_name
            );
            
            $insert_SQL = "INSERT INTO booking_payment_details (event_id,txnid,firstname,lastname,email,phone_no,productinfo,amount,merchant_key,hash,created_by,created_datetime,counter,payment_status,bulk_upload_flag,bulk_upload_group_name) VALUES(:event_id,:txnid,:firstname,:lastname,:email,:phone_no,:productinfo,:amount,:merchant_key,:hash,:created_by,:created_datetime,:counter,:payment_status,:bulk_upload_flag,:bulk_upload_group_name)";
            DB::insert($insert_SQL, $Bindings);
            $BookingPaymentId = DB::getPdo()->lastInsertId();
            
            // $BookingPaymentId = 3407;
            //----------------------------- insert data for (event_booking) table
            $Binding1 = array(
                "event_id" => $event_id,
                "user_id" => $userId,
                "booking_date" => strtotime("now"),
                "total_amount" => !empty($Final_calculated_ticket_amount) ? $Final_calculated_ticket_amount : 0,
                "total_discount" => '0.00',
                "cart_details" => !empty($cart_details_array) ? json_encode($cart_details_array) : '[{}]',  // json_encode($GstArray),
                "transaction_status" => !empty($Final_calculated_ticket_amount) ? 1 : 3,
                "booking_pay_id" => $BookingPaymentId
            );
            // dd($Binding1);
            $Sql1 = "INSERT INTO event_booking (event_id,user_id,booking_date,total_amount,total_discount,cart_details,transaction_status,booking_pay_id) VALUES (:event_id,:user_id,:booking_date,:total_amount,:total_discount,:cart_details,:transaction_status,:booking_pay_id)";
            DB::insert($Sql1, $Binding1);
            $BookingId = DB::getPdo()->lastInsertId();
            
            // $BookingId = 3271;
            //----------------------------- insert data for (booking_details) table
            if(!empty($AllTickets)){

                #booking_details
                $BookingDetailsIds = [];

                foreach ($AllTickets as $ticket) {
                    if (!empty($ticket->count)) {
                        $Binding2 = [];
                        $Sql2 = "";
                        $Binding2 = array(
                            "booking_id" => $BookingId,
                            "event_id" => $event_id,
                            "user_id" => $userId,
                            "ticket_id" => $ticket->id,
                            "quantity" => $ticket->count,
                            "ticket_amount" => !empty($ticket->single_ticket_price) && isset($ticket->single_ticket_price) ? $ticket->single_ticket_price : '0.00', 
                            "ticket_discount" => isset($ticket->ticket_discount) ? ($ticket->ticket_discount) : 0,
                            "booking_date" => strtotime("now"),
                        );
                        $Sql2 = "INSERT INTO booking_details (booking_id,event_id,user_id,ticket_id,quantity,ticket_amount,ticket_discount,booking_date) VALUES (:booking_id,:event_id,:user_id,:ticket_id,:quantity,:ticket_amount,:ticket_discount,:booking_date)";
                        $aResult = DB::insert($Sql2, $Binding2);
                        #Get the last inserted id of booking_details
                        $BookingDetailsId = DB::getPdo()->lastInsertId();

                        $BookingDetailsIds[$ticket->id] = $BookingDetailsId;
                        $new_ticket_id = !empty($aResult[0]->ticket_id) ? $aResult[0]->ticket_id : $ticket->id;

                    }
                }
            }
           
            //----------------------------- insert data for (attendee_booking_details) table
            $separatedArrays = [];
            foreach ($FinalFormQuestions as $key => $arrays) {
                foreach ($arrays as $subArray) {
                    $separatedArrays[] = json_encode($subArray);
                }
            }
            // dd($separatedArrays); 
            $Total_Organiser = 0;
            foreach ($separatedArrays as $key => $value) {
                $subArray = [];
                $subArray = json_decode($value);
                $ticket_price1 = !empty($subArray->ticket_amount) ? $subArray->ticket_amount : 0;
               // dd($ticket_price);
                
                $attendee_details = json_encode(json_encode($subArray->form_array));
                // dd($attendee_details);
                //-----------single ticket calculations                    
                if(!empty($aCatChargesResult)){
                    for ($i=0; $i < count($aCatChargesResult); $i++) { 
                        // dd($aCatChargesResult[$i]->convenience_fee);
                        if ($aCatChargesResult[$i]->registration_amount >= floatval($ticket_price1)){
                            //console.log($aCatChargesResultDetails[i]['convenience_fee']);
                            $ConvenienceFeeBase = ($aCatChargesResult[$i]->convenience_fee);
                            $NewPlatformFee = ($aCatChargesResult[$i]->platform_fee);       // 5 Rs
                            $NewPaymentGatewayFee = ($aCatChargesResult[$i]->payment_gateway_fee); // 1.85 %
                            break;
                        }else if($i == (count($aCatChargesResult)-1) && $aCatChargesResult[$i]->registration_amount <= floatval($ticket_price1)){
                            //console.log($aCatChargesResult[i]['convenience_fee']);
                            $ConvenienceFeeBase = ($aCatChargesResult[$i]->convenience_fee);
                            $NewPlatformFee = ($aCatChargesResult[$i]->platform_fee);       // 5 Rs
                            $NewPaymentGatewayFee = ($aCatChargesResult[$i]->payment_gateway_fee); // 1.85 %
                            break;
                        }
                    }
                }
               // dd($ConvenienceFeeBase,$NewPlatformFee,$NewPaymentGatewayFee);
                if ($event_Result[0]->collect_gst == 1 && $event_Result[0]->prices_taxes_status == 2) {
                    $BasePriceGst = floatval($ticket_price1) != 0 ? floatval($ticket_price1) * ($GstPercentage / 100) : 0; // GST %
                    $Basic_Amount_Gst = (floatval($BasePriceGst) + floatval($ticket_price1));
                } else {
                    $BasePriceGst = '0.00';
                    $Basic_Amount_Gst = floatval($ticket_price1); // registration amt
                }
               
                // dd($ConvenienceFeeBase,$NewPlatformFee,$NewPaymentGatewayFee,$Basic_Amount_Gst);
                if((int)$ConvenienceFeeBase == 30 || (int)$ConvenienceFeeBase == 40 || (int)$ConvenienceFeeBase == 10){
                    //console.log('ss');
                    $Convenience_Fee_Amount = (int)$ConvenienceFeeBase;
                }else{
                    $Convenience_Fee_Amount = $Basic_Amount_Gst * ((int)$ConvenienceFeeBase / 100);  
                }

                $GST_On_Convenience_Fees = floatval($Convenience_Fee_Amount) * ($Convenience_Fees_Gst_Percentage / 100); // GST 18%
                $Total_Convenience_Fees = (floatval($Convenience_Fee_Amount) + $GST_On_Convenience_Fees);
                $GST_On_Platform_Fees_Amount = $NewPlatformFee * ($GST_On_Platform_Fees / 100); // GST 18%
                $Total_Platform_Fees = (floatval($NewPlatformFee) + floatval($GST_On_Platform_Fees_Amount));
                $Net_Registration_Amount = (floatval($Basic_Amount_Gst) + floatval($Total_Convenience_Fees) + floatval($Total_Platform_Fees));

                    if((int)$subArray->player_of_fee == 1 && (int)$subArray->player_of_gateway_fee == 1) {  //Organiser + Organiser
        
                        $Payment_Gateway_Buyer = $Basic_Amount_Gst * ($NewPaymentGatewayFee / 100); // 1.85%
                        $Payment_Gateway_gst_amount = $Payment_Gateway_Buyer * ($Payment_Gateway_Gst / 100); //18%
                        $Total_Payment_Gateway = (floatval($Payment_Gateway_Buyer) + floatval($Payment_Gateway_gst_amount));
                        $BuyerPayment = $Basic_Amount_Gst;  // yes
                        $totalPlatformFee = 0;
                        $totalTaxes = floatval($BasePriceGst);

                    }else if((int)$subArray->player_of_fee == 2 && (int)$subArray->player_of_gateway_fee == 2) {  // Participant + Participant
                        
                        $Payment_Gateway_Buyer = $Net_Registration_Amount * ($NewPaymentGatewayFee / 100); // 1.85%
                        $Payment_Gateway_gst_amount = $Payment_Gateway_Buyer * ($Payment_Gateway_Gst / 100); //18%
                        $Total_Payment_Gateway = (floatval($Payment_Gateway_Buyer) + floatval($Payment_Gateway_gst_amount));
                        $BuyerPayment = (floatval($Total_Payment_Gateway) + floatval($Net_Registration_Amount));
                        // dd($Convenience_Fee_Amount, $NewPlatformFee, $Payment_Gateway_Buyer);
                        $totalPlatformFee = floatval($Convenience_Fee_Amount) + floatval($NewPlatformFee) + floatval($Payment_Gateway_Buyer);
                        $totalTaxes = floatval($BasePriceGst) + floatval($GST_On_Convenience_Fees) + floatval($GST_On_Platform_Fees_Amount) + floatval($Payment_Gateway_gst_amount);

                    }else if((int)$subArray->player_of_fee == 1 && (int)$subArray->player_of_gateway_fee == 2) { // Organiser + Participant
                        
                        $Payment_Gateway_Buyer = $Basic_Amount_Gst * ($NewPaymentGatewayFee / 100); // 1.85%
                        $Payment_Gateway_gst_amount = $Payment_Gateway_Buyer * ($Payment_Gateway_Gst / 100); //18%
                        $Total_Payment_Gateway = (floatval($Payment_Gateway_Buyer) + floatval($Payment_Gateway_gst_amount));
                        $BuyerPayment = (floatval($Basic_Amount_Gst) + floatval($Total_Payment_Gateway));
                        $totalPlatformFee = floatval($Payment_Gateway_Buyer);
                        $totalTaxes = floatval($BasePriceGst) + floatval($Payment_Gateway_gst_amount);

                    }else if((int)$subArray->player_of_fee == 2 && (int)$subArray->player_of_gateway_fee == 1) { // Participant + Organiser
                        
                        $Payment_Gateway_gst_amount = $Payment_Gateway_Buyer * ($Payment_Gateway_Gst / 100); //18%
                        $Total_Payment_Gateway = (floatval($Payment_Gateway_Buyer) + floatval($Payment_Gateway_gst_amount));
                        $BuyerPayment = (floatval($Basic_Amount_Gst) + floatval($Total_Convenience_Fees) + floatval($Total_Platform_Fees) );
                        $totalPlatformFee = floatval($Convenience_Fee_Amount) + floatval($NewPlatformFee);
                        $totalTaxes = floatval($BasePriceGst) + floatval($GST_On_Convenience_Fees) + floatval($GST_On_Platform_Fees_Amount);
                    }

                    $Total_Organiser = (floatval($BuyerPayment) - floatval($Total_Payment_Gateway) - floatval($Total_Platform_Fees) - floatval($Total_Convenience_Fees));
               
                    if(!empty($ticket_price1) || $ticket_price1 != 0){
                        $cart_details_array = ["ExcPriceTaxesStatus" => $event_Result[0]->prices_taxes_status, "Ticket_count" => 1, "Ticket_price" => $ticket_price1, "Registration_Fee_GST" => $BasePriceGst, "Applied_Coupon_Amount" => 0, "Extra_amount" => 0, "Extra_amount_pg_charges" => 0, "Extra_amount_pg_GST" => 0, "Pass_Bare" => $subArray->player_of_fee, "Pg_Bare" => $subArray->player_of_gateway_fee, "Convenience_fee" => $Convenience_Fee_Amount, "Convenience_Fee_GST" => $GST_On_Convenience_Fees, "Platform_fee" => $NewPlatformFee, "Platform_Fee_GST" => $GST_On_Platform_Fees_Amount, "Payment_gateway_charges" => $Payment_Gateway_Buyer, "Payment_Gateway_GST" => $Payment_Gateway_gst_amount, "Organiser_amount" => $Total_Organiser, "Final_total_amount" => $BuyerPayment ];
                    }else{
                        $cart_details_array = ["ExcPriceTaxesStatus" => $event_Result[0]->prices_taxes_status, "Ticket_count" => 1, "Ticket_price" => 0, "Registration_Fee_GST" => 0, "Applied_Coupon_Amount" => 0, "Extra_amount" => 0, "Extra_amount_pg_charges" => 0, "Extra_amount_pg_GST" => 0, "Pass_Bare" => 0, "Pg_Bare" => 0, "Convenience_fee" => 0, "Convenience_Fee_GST" => 0, "Platform_fee" => 0, "Platform_Fee_GST" => 0, "Payment_gateway_charges" => 0, "Payment_Gateway_GST" => 0, "Organiser_amount" => 0, "Final_total_amount" => 0 ];
                    }
               
               // dd($cart_details_array);
                //--------------------------------
                $TicketId = 0;
                $participants_files = [];
                // dd($subArray);
                $isSendEmail = 0;
                foreach ($subArray->form_array as $key => $sArray) {
                    if (isset($sArray->question_form_name)) {
                        if ($sArray->question_form_name == 'first_name') {
                            $first_name = $sArray->ActualValue;
                        } elseif ($sArray->question_form_name == 'last_name') {
                            $last_name = $sArray->ActualValue;
                        } elseif ($sArray->question_form_type == 'email') {
                            // $email = $sArray->ActualValue;
                            if($isSendEmail == 0){
                                $email = $sArray->ActualValue;
                                $isSendEmail = 1;
                            }
                        } elseif ($sArray->question_form_type == 'mobile' && $sArray->question_label == 'Mobile Number') {
                            $mobile = $sArray->ActualValue;
                        } elseif ($sArray->question_form_type == 'file') {
                            $participants_files[] = $sArray->ActualValue;
                        }
                    }
                    if (empty($TicketId)) {
                        $TicketId = !empty($sArray->TicketId) ? $sArray->TicketId : 0;
                    }
                }
                        
                $IdBookingDetails = isset($BookingDetailsIds[$TicketId]) ? $BookingDetailsIds[$TicketId] : 0;
                $sql = "INSERT INTO attendee_booking_details (booking_details_id,ticket_id,attendee_details,email,firstname,lastname,mobile,created_at,ticket_price,final_ticket_price,bulk_upload_flag,cart_detail) VALUES (:booking_details_id,:ticket_id,:attendee_details,:email,:firstname,:lastname,:mobile,:created_at,:ticket_price,:final_ticket_price,:bulk_upload_flag,:cart_detail)";
                $Bind1 = array(
                    "booking_details_id" => !empty($IdBookingDetails) ? $IdBookingDetails : 0,
                    "ticket_id" => !empty($TicketId) ? $TicketId : $new_ticket_id,
                    "attendee_details" => $attendee_details,
                    "email" => isset($email) ? strtolower($email) : '',
                    "firstname" => isset($first_name) ? strtolower($first_name) : '',
                    "lastname" => isset($last_name) ? strtolower($last_name) : '',
                    "mobile" => isset($mobile) ? $mobile : '',
                    "created_at" => strtotime("now"),
                    "ticket_price" => $ticket_price1,
                    "final_ticket_price" => !empty($ticket_price1) ? $BuyerPayment : 0,
                    "bulk_upload_flag" => 1,
                    "cart_detail" => json_encode($cart_details_array) 

                );
                DB::insert($sql, $Bind1);
                $attendeeId = DB::getPdo()->lastInsertId();
                // dd($attendeeId);
                      
                $booking_date = 0;
                $bd_sql = "SELECT booking_date FROM booking_details WHERE id = :booking_details_id";
                $bd_bind = DB::select($bd_sql, array("booking_details_id" => $BookingDetailsId));
                if (count($bd_bind) > 0) {
                    $booking_date = $bd_bind[0]->booking_date;
                }
                $uniqueId = 0;
                $uniqueId = $event_id . "-" . $attendeeId . "-" . $booking_date;
                // dd($uniqueId,$IdBookingDetails,$booking_date);
                $u_sql = "UPDATE attendee_booking_details SET registration_id=:registration_id WHERE id=:id";
                $u_bind = DB::update($u_sql, array("registration_id" => $uniqueId, 'id' => $attendeeId));      
            }

            //-----------------------------------------------------
            $this->returnData['BookPayId'] = $BookingPaymentId;
        }

         // dd($last_inserted_id);
        return $this->returnData;
    }
}
