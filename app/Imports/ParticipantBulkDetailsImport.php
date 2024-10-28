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
        // dd($collection);
     
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
            
            // dd($collection);
            foreach ($collection as $key=>$row)
            {
                //echo $row['agent_type'].'<br>'
                if($row->filter()->isNotEmpty()){
                    
                    $exSheetFarmerName = isset($row['farmer_name']) ? $row['farmer_name']: '';
                }
            }          
        }
        return $this->returnData;
    }
}
