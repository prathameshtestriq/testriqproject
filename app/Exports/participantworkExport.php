<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Support\Facades\DB;

class participantworkExport implements FromArray, WithHeadings, ShouldAutoSize, WithEvents
{
    protected $request;
    protected $questionLabels;

    public function __construct()
    {
        $event_name = Session::has('search_event') ? Session::get('search_event') : '';
    //    dd( $event_name );
        // Fetch question labels and store them as headings
        $SQL = "SELECT question_label FROM event_form_question WHERE event_id = ".$event_name." ORDER BY sort_order ASC";
        $this->questionLabels = DB::select($SQL, array());
    }

    public function array(): array
    {
        $excelData = [];
        return $excelData;
      
    }

    public function headings(): array
    {
        $customHeadings = [
            'Ticket Name'
        ];

         // Retrieve the question labels from the database and convert them to an array of strings
            $questionHeadings = array_map(function ($label) {
                return $label->question_label;
            }, $this->questionLabels);

            // Merge custom headings with question headings
            return array_merge($customHeadings, $questionHeadings);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                // Apply bold formatting to the first row (headings)
                $event->sheet->getStyle('A1:' . 'Z1')->getFont()->setBold(true);
            },
        ];
    }
}
