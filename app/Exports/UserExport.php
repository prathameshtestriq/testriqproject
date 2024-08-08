<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Support\Facades\DB;

class UserExport implements FromArray, WithHeadings, ShouldAutoSize, WithEvents
{
    protected $users;

    public function __construct()
    {
       
    }

    public function array(): array
    {
      

        // Collect session data for filtering
        $user_name = Session::has('name') ? Session::get('name') : '';
        $status = Session::has('status') ? Session::get('status') : '';
        $mobile = Session::has('mobile') ? Session::get('mobile') : '';
        $email = Session::has('email') ? Session::get('email') : '';
    
       // Build the SQL query with search criteria
       $s_sql = 'SELECT u.id, u.firstname,u.lastname,u.is_active,u.email,u.mobile,u.gender,u.dob,(SELECT name FROM  states s WHERE u.state = s.id) as state_name,(SELECT name FROM  cities s WHERE u.city = s.id) as city_name
       FROM users u WHERE 1=1';
        // Add conditions based on session data
    
        if(!empty( $user_name)){
            $s_sql .= ' AND (LOWER((CONCAT(u.firstname, " ", u.lastname))) LIKE \'%' . strtolower($user_name) . '%\')';
        } 

        if(!empty(  $email)){
            $s_sql .= ' AND (LOWER(u.email) LIKE \'%' . strtolower( $email) . '%\')';
        } 

        if(!empty(  $mobile)){
            $s_sql .= ' AND (LOWER(u.mobile) LIKE \'%' . strtolower( $mobile) . '%\')';
        } 

        if(isset( $status)){
            $s_sql .= ' AND (LOWER(u.is_active) LIKE \'%' . strtolower($status) . '%\')';
        } 

        $user_details = DB::select($s_sql, array());
        //   dd($user_details);
            $excelData = [];
            foreach ($user_details as $val) {
                if ($val->gender == 1) {
                    $Gender = 'Male';
                } elseif ($val->gender == 2) {
                    $Gender = 'Female';
                } else {
                    $Gender = 'Other';
                }
            
                $excelData[] = array(
                    'User Name' => $val->firstname . ' ' . $val->lastname,
                    'Email ' => $val->email,
                    'Mobile' => $val->mobile,
                    'Gender' => $Gender,
                    'DOB'    => date('d-m-Y',strtotime($val->dob)),
                    'State' =>$val->state_name,
                    'City' => $val->city_name,
                    'Percentage'=> '%',
                );
            }

        return $excelData;
    }

    public function headings(): array
    {      
        return [
            [
                'User Name',
                'Email',
                'Mobile',
                'Gender',
                'DOB',
                'State',
                'City',
                'Percentage'
            ]
        ];
    }


    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Set horizontal alignment for all cells
                $sheet->getStyle('A1:H1')->getAlignment()->setHorizontal('left');

                // Apply font styling to header
                $sheet->getStyle('A1:H1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ]
                ]);
            },
        ];
    }
}
