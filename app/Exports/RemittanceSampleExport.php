<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RemittanceSampleExport implements FromArray, WithHeadings, WithStyles
{
    public function headings(): array
    {
        return [
            'Remittance Name',
            'Remittance Date',
            'Event Name',
            'Gross Amount',
            'Service Charge',
            'SGST',
            'CGST',
            'IGST',
            'Deductions',
            'TDS',
            'Amount Remitted',
            'Bank Reference'
        ];
    }

    public function array(): array
    {
        return [];
        // Sample data
        // return [
        //     [
        //         'Sample Remittance', '2024-09-03', 'Sample Event', '1000', '100', '18', '18', '0', '50', '10', '900', 'REF123456'
        //     ],
        //     // Add more rows as needed
        // ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Make the first row (headings) bold
            1 => ['font' => ['bold' => true]],
        ];
    }
}

