<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Support\Facades\DB;

class RemittanceManagementExport implements FromArray, WithHeadings, ShouldAutoSize, WithEvents
{
    protected $request;

  

    public function array(): array
    {
        
        // dd("here");
        // Collect session data for filtering
        $remittance_name = Session::has('remittance_name') ? Session::get('remittance_name') : '';
        $start_remittance_date = Session::has('start_remittance_date') ? Session::get('start_remittance_date') : '';
        $end_remittance_date = Session::has('end_remittance_date') ? Session::get('end_remittance_date') : '';
        $remittance_status = Session::has('remittance_status') ? Session::get('remittance_status') : '';
       
       // Build the SQL query with search criteria
       $s_sql = 'SELECT * FROM remittance_management rm where 1=1';

       if (!empty($remittance_name)) {
           $s_sql .= ' AND LOWER(rm.remittance_name) LIKE \'%' . strtolower($remittance_name) . '%\'';
       }

       if(!empty($start_remittance_date)){
           $startdate = strtotime($start_remittance_date);    
           $s_sql .= " AND rm.remittance_date >= "." $startdate";
           // dd($sSQL);
       }

       if(!empty($end_remittance_date)){
           $endDate = strtotime($end_remittance_date);
           $s_sql .= " AND  rm.remittance_date <="." $endDate";
           // dd($sSQL);
       } 

       if(isset( $remittance_status)){
           $s_sql .= ' AND (LOWER(rm.active) LIKE \'%' . strtolower($remittance_status) . '%\')';
       } 

        $remittance_management = DB::select($s_sql, array());

        $excelData = [];
        foreach ($remittance_management as $val) {
        //   dd($val);
            $excelData[] = array(
                'REMITTANCE NAME' => $val->remittance_name ,
                'REMITTANCE DATE' => date('d-m-Y H:i:s', $val->remittance_date),
                'GROSS AMOUNT' => $val->gross_amount,
                'SERVICE CHARGE' => $val->service_charge,
                'SGST' => $val->Sgst,
                'CGST' => $val->Cgst ,
                'IGST' => $val->Igst, 
                'DEDUCTIONS' => $val->deductions,
                'TDS' => $val->Tds,
                'AMOUNT REMITTED' => $val->amount_remitted,
                'BANK REFERENCE' => $val->bank_reference,
               
            );
            // dd($excelData);
        }

        return $excelData;
    }

    public function headings(): array
    {

        return [
            [
                'Remittance Name',
                'Remittance Date',
                'Gross Amount',
                'Service Charge',
                'Sgst',
                'Cgst',
                'Igst',
                'Deductions',
                'Tds ',
                'Amount Remitted',
                'Bank Reference'
            ]
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Set horizontal alignment for all cells
                $sheet->getStyle('A1:L1')->getAlignment()->setHorizontal('left');

                // Merge cells in the header
                // $headerMergeRanges = ['A1:L1', 'A2:L2', 'A3:L3'];
                // $headerMergeRanges = ['A1:L1'];
                // foreach ($headerMergeRanges as $range) {
                //     $sheet->mergeCells($range);
                // }

                // Set row heights
                // for ($row = 1; $row <= 4; $row++) {
                //     $sheet->getRowDimension($row)->setRowHeight(25);
                // }

                // Apply font styling to header
                $sheet->getStyle('A1:L1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ]
                ]);
            },
        ];
    }
}
