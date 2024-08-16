<?php

namespace App\Imports;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Facades\Session;


class EmailSendingImport implements ToCollection
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
                $Email = (isset($row[0])) ? trim(strtolower($row[0])) : '';
              

                $error = false;
                $errorMessage = '';

                if(empty($Email)){
                    $error = true;
                    $errorMessage = 'Email is empty';
                }
             
              
                if(!$error){
                    // $s_sql = 'SELECT * FROM remittance_management rm where rm.remittance_name = :Remittance_Name';
                    // $remittance_details = DB::select($s_sql, array(
                    //     'Remittance_Name'=>$Remittance_Name
                    // ));
            

                    // if(!$remittance_details){
                
                        $Bindings = [
                            'email' =>  $Email,
                            
                        ];
                        // dd(   $Bindings);

                        $ssql = 'INSERT INTO send_email_log(email)
                                VALUES (:email)';
                        DB::insert($ssql,$Bindings);
                        $aReturn['success']++;

                    // }else{

                    //     $aReturn['error']++;
                    //     $aReturn['message'] = '<br>'.'Row '.$row_no.' - Already exists.';
                       
                    // }

                }else{
                    $aReturn['error']++;
                    $aReturn['message'] = '<br>'.'Row '.$row_no.' - '.$errorMessage;
                }
        }

        $Message =  'Total remittance - '.$aReturn['total'].'<br>';
        $Message .= 'Remittances details updated successfully - '.$aReturn['success'].'';
        $Message .= $aReturn['message'];

        Session::flash('success',$Message);
       
    }
}