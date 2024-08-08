<?php

namespace App\Imports;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Facades\Session;


class RemittanceDetailsImport implements ToCollection
{
    /**
     * @return \Illuminate\Support\Collection
     */

    // public function __construct()
    // {
    //     //  $this->divisionId= $divisionId;
    // }
    public function collection(Collection $rows)
    {
       
        if(empty($rows) || count($rows) == 1){
            Session::flash('error','There is no data in provided file');
            return false;
        }
        
        $aReturn = array(
            'total' => -1,
            'success' => 0,
            'error' => 0,
            'message' => '' 
        );

        $row_no = 0;
        $i = 0;
        // $j = 0;
    
        foreach ($rows as $row) {

                $aReturn['total'] += 1;

                $i++; 
                $row_no++;
                if($i == 1){
                    continue;
                }
            
                // $srno = (isset($row[0])) ? trim(strtolower($row[0])) : '';
                $Remittance_Name = (isset($row[0])) ? trim(strtolower($row[0])) : '';
                $Remittance_Date = (isset($row[1])) ? trim(strtolower($row[1])) : '';
                $Gross_Amount = (isset($row[2])) ? trim($row[2]) : '';
                $Service_Charge = (isset($row[3])) ? trim($row[3]) : '';
                $Sgst = (isset($row[4])) ? trim($row[4]) : '';
                $Cgst = (isset($row[5])) ? trim($row[5]) : '';
                $Igst = (isset($row[6])) ? trim($row[6]) : '';
                $Deductions = (isset($row[7])) ? $row[7] : 0;
                $Tds = (isset($row[8])) ? $row[8] : 0;
                $Amount_Remitted = (isset($row[9])) ? trim(strtolower($row[9])) : '';
                $Bank_Reference = (isset($row[10])) ? trim(strtolower($row[10])) : '';

                $error = false;
                $errorMessage = '';

                if(empty( $Remittance_Name)){
                    $error = true;
                    $errorMessage = 'Remittance Name is empty';
                }
                if(empty( $Remittance_Date)){
                    $error = true;
                    $errorMessage = 'Remittance Date is empty';
                }
                if(empty( $Gross_Amount)){
                    $error = true;
                    $errorMessage = 'Gross Amount is empty';
                }
                if(empty( $Service_Charge)){
                    $error = true;
                    $errorMessage = 'Service Charge is empty';
                }
                if(empty( $Sgst)){
                    $error = true;
                    $errorMessage = 'Sgst is empty';
                }
                if(empty( $Cgst)){
                    $error = true;
                    $errorMessage = 'Cgst is empty';
                }
                if(empty( $Igst)){
                    $error = true;
                    $errorMessage = 'Igst is empty';
                }
                if(empty( $Deductions)){
                    $error = true;
                    $errorMessage = 'Deductions is empty';
                }
                if(empty( $Tds)){
                    $error = true;
                    $errorMessage = 'Tds is empty';
                }
                if(empty( $Amount_Remitted)){
                    $error = true;
                    $errorMessage = 'Amount Remitted is empty';
                }
                if(empty( $Bank_Reference)){
                    $error = true;
                    $errorMessage = 'Bank Reference is empty';
                }
              
                if(!$error){
                    $s_sql = 'SELECT * FROM remittance_management rm where rm.remittance_name = :Remittance_Name';
                    $remittance_details = DB::select($s_sql, array(
                        'Remittance_Name'=>$Remittance_Name
                    ));
            

                    if(!$remittance_details){
                
                        $Bindings = [
                            'remittance_name' =>  $Remittance_Name,
                            'remittance_date' => strtotime( $Remittance_Date),
                            'gross_amount' =>  $Gross_Amount,
                            'service_charge' =>  $Service_Charge,
                            'Sgst' => $Sgst,
                            'Cgst' => $Cgst,
                            'Igst' => $Igst,
                            'deductions' => $Deductions,
                            'Tds' => $Tds,
                            'amount_remitted' => $Amount_Remitted,
                            'bank_reference' =>  $Bank_Reference
                        ];
                        // dd(   $Bindings);

                        $ssql = 'INSERT INTO remittance_management(
                            remittance_name,remittance_date,gross_amount,service_charge,
                            Sgst,Cgst,Igst,deductions,Tds,amount_remitted,bank_reference)
                                VALUES (
                            :remittance_name,:remittance_date,:gross_amount,:service_charge,
                            :Sgst,:Cgst,:Igst,:deductions,:Tds,:amount_remitted,:bank_reference
                            )';
                        DB::insert($ssql,$Bindings);
                        $aReturn['success']++;

                    }else{

                        $aReturn['error']++;
                        $aReturn['message'] = '<br>'.'Row '.$row_no.' - Already exists.';
                       
                    }

                }else{
                    $aReturn['error']++;
                    $aReturn['message'] = '<br>'.'Row '.$row_no.' - '.$errorMessage;
                }
        }

        $Message =  'Total remittance - '.$aReturn['total'].'<br>';
        $Message .= 'Remittances details updated successfully - '.$aReturn['success'].'';
        $Message .= $aReturn['message'];

        // dd($Message);

        Session::flash('success',$Message);
       
    }
}