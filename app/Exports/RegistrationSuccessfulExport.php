<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Support\Facades\DB;

class RegistrationSuccessfulExport implements FromArray, WithHeadings, ShouldAutoSize, WithEvents
{
    protected $eventId;

    public function __construct($eventId)
    {
        $this->eventId = $eventId;
    }

    public function array(): array
    {
        
        $eventId = $this->eventId;
        // // Collect session data for filtering
        $registration_user_name = Session::has('registration_user_name') ? Session::get('registration_user_name') : '';
        $registration_transaction_status = Session::has('registration_transaction_status') ? Session::get('registration_transaction_status') : '';
        $start_registration_booking_date = Session::has('start_registration_booking_date') ? Session::get('start_registration_booking_date') : '';
        $end_registration_booking_date = Session::has('end_registration_booking_date') ? Session::get('end_registration_booking_date') : '';

        // // Build the SQL query with search criteria
        $s_sql = "SELECT eb.id AS EventBookingId,eb.user_id,eb.booking_date,eb.total_amount AS TotalAmount,eb.transaction_status,
        SUM(bd.quantity) AS TotalTickets,u.id,u.firstname,u.lastname,u.email,u.mobile
        FROM event_booking AS eb 
        LEFT JOIN booking_details AS bd ON bd.booking_id = eb.id
        LEFT JOIN users AS u ON u.id = eb.user_id
        WHERE eb.event_id= ".$eventId ;

        // // Add conditions based on session data
        if (!empty($registration_user_name)) {
            $s_sql .= ' AND (LOWER((CONCAT(u.firstname, " ", u.lastname))) LIKE \'%' . strtolower($registration_user_name) . '%\')';
        }

       
        if(isset( $registration_transaction_status)){
            $s_sql .= ' AND (LOWER( eb.transaction_status) LIKE \'%' . strtolower($registration_transaction_status) . '%\')';
        } 
       

        if(!empty($start_registration_booking_date)){
            $startdate = strtotime($start_registration_booking_date);  
            $s_sql .= " AND eb.booking_date >= "." $startdate";
            // dd($sSQL);
        }

        if(!empty($end_registration_booking_date)){
            $endDate = strtotime($end_registration_booking_date);
            $s_sql .= " AND  eb.booking_date <="." $endDate";
            // dd($sSQL);
        } 

        $s_sql .= " GROUP BY bd.booking_id";
        $s_sql .= " ORDER BY eb.id DESC";

        
        $registration_successful = DB::select($s_sql, array());

        $excelData = [];
        foreach ($registration_successful as $val) {
            $excelData[] = array(
                'User Name' => $val->firstname . ' ' . $val->lastname,
                'Email' => $val->email,
                'Mobile' => $val->mobile,
                'Number of tickets' => $val->TotalTickets,
                'Total amount' => $val->TotalAmount,
                'Booking Date' => date('d-m-Y H:i:s', $val->booking_date),
                'Transaction status' => 
                 ($val->transaction_status == 0 ? "Initiate" :
                ($val->transaction_status == 1 ? "Success" :
                 ($val->transaction_status == 2 ? "Fail" :
                  ($val->transaction_status == 3 ? "Free" : "Unknown")))),
              
            );
            // dd($excelData);
        }

        return $excelData;
    }

    public function headings(): array
    {
        $eventId = $this->eventId;

        return [
            ['Report Name: Registration Successful'],
            ['Event Id :' . $eventId],
            [],
            [
                'User Name',
                'Email',
                'Mobile',
                'Number Of Tickets',
                'Total Amount',
                'Booking Date',
                'Transaction Status',
            ]
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Set horizontal alignment for all cells
                $sheet->getStyle('A1:G1')->getAlignment()->setHorizontal('left');

                // Merge cells in the header
                $headerMergeRanges = ['A1:G1', 'A2:G2', 'A3:G3'];
                foreach ($headerMergeRanges as $range) {
                    $sheet->mergeCells($range);
                }

                // Set row heights
                for ($row = 1; $row <= 4; $row++) {
                    $sheet->getRowDimension($row)->setRowHeight(25);
                }

                // Apply font styling to header
                $sheet->getStyle('A1:G4')->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ]
                ]);
            },
        ];
    }
}
