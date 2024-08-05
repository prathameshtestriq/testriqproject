<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Support\Facades\DB;

class PaymentLogExport implements FromArray, WithHeadings, ShouldAutoSize, WithEvents
{
    protected $request;

  

    public function array(): array
    {
        
        
        // Collect session data for filtering
        $user_name = Session::has('user_name') ? Session::get('user_name') : '';
        $start_payment_date = Session::has('start_payment_date') ? Session::get('start_payment_date') : '';
        $end_payment_date = Session::has('end_payment_date') ? Session::get('end_payment_date') : '';
        $transaction_id_payment = Session::has('transaction_id_payment') ? Session::get('transaction_id_payment') : '';
        $transaction_status_payment = Session::has('transaction_status_payment') ? Session::get('transaction_status_payment') : '';
       
        // // Build the SQL query with search criteria
        $s_sql = "SELECT p.id AS paymentId,p.txnid,p.amount,p.post_data,p.payment_status,p.created_datetime,u.id AS userId,u.firstname,u.lastname,u.email,u.mobile 
        FROM booking_payment_details AS p 
        LEFT JOIN users AS u ON u.id=p.created_by
        WHERE 1=1";

        if(!empty( $user_name)){
            $s_sql .= ' AND (LOWER((CONCAT(u.firstname, " ", u.lastname))) LIKE \'%' . strtolower($user_name) . '%\')';
        } 

        if(!empty($start_payment_date)){
            $startdate = strtotime($start_payment_date);    
            $s_sql .= " AND p.created_datetime >= "." $startdate";
            // dd($sSQL);
        }

        if(!empty($end_payment_date)){
            $endDate = strtotime($end_payment_date);
            $s_sql .= " AND  p.created_datetime <="." $endDate";
            // dd($sSQL);
        } 

        
        if (!empty($transaction_id_payment)) {
            $s_sql .= ' AND LOWER( p.txnid) LIKE \'%' . strtolower($transaction_id_payment) . '%\'';
        }
     
        if(isset( $transaction_status_payment)){
            $s_sql .= ' AND (LOWER(p.payment_status) LIKE \'%' . strtolower($transaction_status_payment) . '%\')';
        } 
        
        $Payment_log = DB::select($s_sql, array());

        $excelData = [];
        foreach ($Payment_log as $val) {
            $data = json_decode( $val->post_data , true); // Decode JSON into an associative array

            if (isset($data['mihpayid'])) {
                $mihpayid = $data['mihpayid'];
            } else {
                $mihpayid = '-'; // Handle cases where JSON is invalid or mihpayid is missing
            }
            $excelData[] = array(
                'User Name' => $val->firstname . ' ' . $val->lastname,
                'Email' => $val->email,
                'Mobile' => $val->mobile,
                'Transaction Id' => $val->txnid,
                'Pay Id' => $mihpayid,
                'Total Amount' => $val->amount ,
                'Payment Date' => date('d-m-Y H:i:s', $val->created_datetime), 
                'Transaction Status' => $val->payment_status,
            );
            // dd($excelData);
        }

        return $excelData;
    }

    public function headings(): array
    {

        return [
            ['Report Name: Payment Log'],
            [],
            [],
            [
                'User Name',
                'Email',
                'Mobile',
                'Transaction Id',
                'Pay Id',
                'Total Amount',
                'Payment Date',
                'Transaction Status'
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

                // Merge cells in the header
                $headerMergeRanges = ['A1:H1', 'A2:H2', 'A3:H3'];
                foreach ($headerMergeRanges as $range) {
                    $sheet->mergeCells($range);
                }

                // Set row heights
                for ($row = 1; $row <= 4; $row++) {
                    $sheet->getRowDimension($row)->setRowHeight(25);
                }

                // Apply font styling to header
                $sheet->getStyle('A1:H4')->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ]
                ]);
            },
        ];
    }
}
