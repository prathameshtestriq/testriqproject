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
    public function collection(Collection $rows)
    {
       
       // dd($rows);
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
        // if(!empty($row)){
        $aReturn['error'] = 0;
            foreach ($rows as $row) {

                    $aReturn['total'] += 1;

                    $i++; 
                    $row_no++;
                    if($i == 1){
                        continue;
                    }
                
                    $Remittance_Name = !empty($row[0]) && isset($row[0]) ? trim(strtolower($row[0])) : '';
                    $Remittance_Date = !empty($row[1]) && isset($row[1]) ? trim(strtolower($row[1])) : '';
                    $Event_Id        = !empty($row[2]) && isset($row[2]) ? trim($row[2]) : '';
                    $Bank_Reference =  !empty($row[3]) && isset($row[3]) ? trim(strtolower($row[3])) : '';
                    $Gross_Amount =    !empty($row[4]) && isset($row[4]) ? trim($row[4]) : '';
                    $Service_Charge =  !empty($row[5]) && isset($row[5]) ? trim($row[5]) : '';
                    $Sgst =            !empty($row[6]) && isset($row[6]) ? trim($row[6]) : '';
                    $Cgst =            !empty($row[7]) && isset($row[7]) ? trim($row[7]) : '';
                    $Igst =            !empty($row[8]) && isset($row[8]) ? trim($row[8]) : '';
                    $Deductions =      !empty($row[9]) && isset($row[9]) ? trim($row[9]) : '';
                    $Tcs =             !empty($row[10]) && isset($row[10]) ? trim($row[10]) : 0;
                    $Tds =             !empty($row[11]) && isset($row[11]) ? trim($row[11]) : 0;
                    $Amount_Remitted = !empty($row[12]) && isset($row[12]) ? trim($row[12]) : 0;

                    $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($Remittance_Date);
                    $Remittance_Final_Date = $date->format('Ymd');
                    //dd($Remittance_Final_Date);

                    $error = false;
                    $errorMessage = '';

                    if(empty($Remittance_Name)){
                        $error = true;
                        $errorMessage = 'Remittance Name is empty';
                    }
                    if(empty($Remittance_Date)){
                        $error = true;
                        $errorMessage = 'Remittance Date is empty';
                    }
                    if(empty($Event_Id)){
                        $error = true;
                        $errorMessage = 'Event Id is empty';
                    }
                    if(empty($Gross_Amount)){
                        $error = true;
                        $errorMessage = 'Gross Amount is empty';
                    }
                  // dd($error,$errorMessage);
                    if(!$error){
                        $s_sql = 'SELECT * FROM remittance_management rm where lower(rm.remittance_name)= :Remittance_Name';
                        $remittance_details = DB::select($s_sql, array(
                            'Remittance_Name' => strtolower($Remittance_Name) 
                        ));
                
                        if(!$remittance_details){
                    
                            $Bindings = [
                                'remittance_name' => $Remittance_Name,
                                'remittance_date' => strtotime($Remittance_Final_Date),
                                'gross_amount'    => $Gross_Amount,
                                'event_id'        => $Event_Id,
                                'service_charge'  => $Service_Charge,
                                'Sgst'            => $Sgst,
                                'Cgst'            => $Cgst,
                                'Igst'            => $Igst,
                                'deductions'      => $Deductions,
                                'Tcs'             => $Tcs,
                                'Tds'             => $Tds,
                                'amount_remitted' => $Amount_Remitted,
                                'bank_reference'  => $Bank_Reference
                            ];
                           
                            $ssql = 'INSERT INTO remittance_management(remittance_name,remittance_date,gross_amount,event_id,service_charge,
                                Sgst,Cgst,Igst,deductions,Tcs,Tds,amount_remitted,bank_reference)
                                    VALUES (:remittance_name,:remittance_date,:gross_amount,:event_id,:service_charge,:Sgst,:Cgst,:Igst,:deductions,:Tcs,:Tds,:amount_remitted,:bank_reference)';
                            DB::insert($ssql,$Bindings);
                            $aReturn['success']++;

                        }else{

                            $aReturn['error']++;
                            $aReturn['message'] = '<br> Remittance Name Already exists - '.$aReturn['error'];
                        }

                    }else{
                        $aReturn['error']++;
                        $aReturn['message1'] = '<br>'.'Row '.$row_no.' - '.$errorMessage;
                    }
            }

            $Message =  'Total remittance - '.$aReturn['total'].'<br>';
            $Message .= 'Remittances details updated successfully - '.$aReturn['success'].'';
            $Message .= $aReturn['message'];
            $Message .= !empty($aReturn['message1']) ? $aReturn['message1'] : '';

            Session::flash('success',$Message);
        //} 
    }
}