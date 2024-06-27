<?php

namespace App\Exports;

use App\Invoice;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class RemittanceDetailsDataExport implements FromView,WithEvents,ShouldAutoSize
{
    protected $AttendeeDataArray;

    protected $procCounts;
   
    //Get modal data report download excel
    public function __construct($AttendeeDataArray)
    {
        $this->procCounts = count($AttendeeDataArray);
        $this->AttendeeDataArray = $AttendeeDataArray;
    }
    public function view(): View
    {
        return view('remittance_details_export_excel', [
            'AttendeeDataArray' => $this->AttendeeDataArray
        ]);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $cols = array_keys($sheet->getColumnDimensions());
                $lastElement = end($cols);
                
                $event->sheet->getStyle('A1:'.$lastElement.$this->procCounts+1)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);

                foreach ($cols as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }
            },
        ];
    }
}