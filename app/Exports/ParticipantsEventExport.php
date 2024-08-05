<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Support\Facades\DB;

class ParticipantsEventExport implements FromArray, WithHeadings, ShouldAutoSize, WithEvents
{
    protected $eventId;

    public function __construct($eventId)
    {
        $this->eventId = $eventId;
    }

    public function array(): array
    {
        $eventId = $this->eventId;

        // Collect session data for filtering
        $participant_name = Session::has('participant_name') ? Session::get('participant_name') : '';
        $transaction_status = Session::has('transaction_status') ? Session::get('transaction_status') : '';
        $registration_id = Session::has('registration_id') ? Session::get('registration_id') : '';
        $mobile_no = Session::has('mobile_no') ? Session::get('mobile_no') : '';
        $email_id = Session::has('email_id') ? Session::get('email_id') : '';
        $category = Session::has('category') ? Session::get('category') : '';
        $start_booking_date = Session::has('start_booking_date') ? Session::get('start_booking_date') : '';
        $end_booking_date = Session::has('end_booking_date') ? Session::get('end_booking_date') : '';

        // Build the SQL query with search criteria
        $sSQL = 'SELECT a.*,e.booking_date,e.booking_pay_id, e.transaction_status, b.event_id,a.id AS aId, CONCAT(a.firstname, " ", a.lastname) AS user_name,
        (SELECT et.ticket_name FROM event_tickets et WHERE et.id = a.ticket_id) AS category_name,
        (SELECT bpd.txnid FROM booking_payment_details bpd WHERE bpd.id = e.booking_pay_id) AS Transaction_order_id,
        (SELECT bpt.mihpayid FROM booking_payment_log bpt WHERE bpt.txnid = Transaction_order_id) AS payu_id
        FROM attendee_booking_details a
        LEFT JOIN booking_details AS b ON a.booking_details_id = b.id
        Inner JOIN event_booking AS e ON b.booking_id = e.id
        WHERE b.event_id = '.$eventId;

        // Add conditions based on session data
        if (!empty($participant_name)) {
            $sSQL .= ' AND (LOWER((CONCAT(a.firstname, " ", a.lastname))) LIKE \'%' . strtolower($participant_name) . '%\')';
        }
        if ($transaction_status !== '') {
            $sSQL .= ' AND e.transaction_status = '.$transaction_status;
        }
        if (!empty($registration_id)) {
            $sSQL .= ' AND a.registration_id LIKE \'%' . strtolower($registration_id) . '%\'';
        }
        if (!empty($mobile_no)) {
            $sSQL .= ' AND a.mobile LIKE \'%' . strtolower($mobile_no) . '%\'';
        }
        if (!empty($email_id)) {
            $sSQL .= ' AND a.email LIKE \'%' . strtolower($email_id) . '%\'';
        }
        if (!empty($category)) {
            $sSQL .= ' AND a.ticket_id = (SELECT id FROM event_tickets WHERE ticket_name LIKE \'%' . strtolower($category) . '%\')';
        }
        if (!empty($start_booking_date)) {
            $sSQL .= ' AND e.booking_date >= '. strtotime($start_booking_date);
        }
        if (!empty($end_booking_date)) {
            $sSQL .= ' AND e.booking_date <= '. strtotime($end_booking_date);
        }

        $sSQL .= ' ORDER BY a.id DESC';

        
        $event_participants = DB::select($sSQL, array());
      
        $excelData = [];
        foreach ($event_participants as $val) {
            $excelData[] = array(
                'Participant Name' => $val->firstname . ' ' . $val->lastname,
                'Booking Date' => date('d-m-Y H:i:s', $val->booking_date),
                'Transaction/order Id' => $val->Transaction_order_id,
                'Registration Id' => $val->registration_id,
                'Payu Id' => $val->payu_id,
                'Free/paid status'=> 0,
                'Coupan Code'=> 0,
                'Total amount'=>0,
                'Transaction Status' => 
                ($val->transaction_status == 0 ? "Initiate" :
                 ($val->transaction_status == 1 ? "Success" :
                  ($val->transaction_status == 2 ? "Fail" :
                   ($val->transaction_status == 3 ? "Free" : "Unknown")))),
                'Email/Mobile No' => $val->email . '  '.$val->mobile,
                'Category Name' => $val->category_name,
                
               
            );
        }

        return $excelData;
    }

    public function headings(): array
    {
        $eventId = $this->eventId;
        $sSQL = 'SELECT name FROM events where id = '.$eventId;
        $event_name = DB::select($sSQL ,array()); 

            // Collect session data for filtering
            $participant_name = Session::has('participant_name') ? Session::get('participant_name') : '';
            $transaction_status = Session::has('transaction_status') ? Session::get('transaction_status') : '';
            $registration_id = Session::has('registration_id') ? Session::get('registration_id') : '';
            $mobile_no = Session::has('mobile_no') ? Session::get('mobile_no') : '';
            $email_id = Session::has('email_id') ? Session::get('email_id') : '';
            $category = Session::has('category') ? Session::get('category') : '';
            $start_booking_date = Session::has('start_booking_date') ? Session::get('start_booking_date') : '';
            $end_booking_date = Session::has('end_booking_date') ? Session::get('end_booking_date') : '';
    
            // Build the SQL query with search criteria
            $sSQL = 'SELECT count(a.id) as count FROM attendee_booking_details a
            LEFT JOIN booking_details AS b ON a.booking_details_id = b.id
            Inner JOIN event_booking AS e ON b.booking_id = e.id
            WHERE b.event_id = '.$eventId;
    
            // Add conditions based on session data
            if (!empty($participant_name)) {
                $sSQL .= ' AND (LOWER((CONCAT(a.firstname, " ", a.lastname))) LIKE \'%' . strtolower($participant_name) . '%\')';
            }
            if ($transaction_status !== '') {
                $sSQL .= ' AND e.transaction_status = '.$transaction_status;
            }
            if (!empty($registration_id)) {
                $sSQL .= ' AND a.registration_id LIKE \'%' . strtolower($registration_id) . '%\'';
            }
            if (!empty($mobile_no)) {
                $sSQL .= ' AND a.mobile LIKE \'%' . strtolower($mobile_no) . '%\'';
            }
            if (!empty($email_id)) {
                $sSQL .= ' AND a.email LIKE \'%' . strtolower($email_id) . '%\'';
            }
            if (!empty($category)) {
                $sSQL .= ' AND a.ticket_id = (SELECT id FROM event_tickets WHERE ticket_name LIKE \'%' . strtolower($category) . '%\')';
            }
            if (!empty($start_booking_date)) {
                $sSQL .= ' AND e.booking_date >= '. strtotime($start_booking_date);
            }
            if (!empty($end_booking_date)) {
                $sSQL .= ' AND e.booking_date <= '. strtotime($end_booking_date);
            }
    
            $sSQL .= ' ORDER BY a.id DESC';
    
            
            $event_participants = DB::select($sSQL, array());
          
      
        return [
            ['Event Name : ' . $event_name[0]->name],
            ['Participant Count : ' .$event_participants[0]->count],
            [],
            [
                'Participant Name',
                'Booking Date',
                'Transaction/order Id',
                'Registration Id',
                'Payu Id',
                'Free/paid status',
                'Coupan Code',
                'Total amount',
                'Payment/Transaction status',
                'Email/Mobile No',
                'Category Name'
            ]
        ];
    }


    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Set horizontal alignment for all cells
                $sheet->getStyle('A1:K1')->getAlignment()->setHorizontal('left');

                // Merge cells in the header
                $headerMergeRanges = ['A1:K1', 'A2:K2', 'A3:K3'];
                foreach ($headerMergeRanges as $range) {
                    $sheet->mergeCells($range);
                }

                // Set row heights
                for ($row = 1; $row <= 4; $row++) {
                    $sheet->getRowDimension($row)->setRowHeight(25);
                }

                // Apply font styling to header
                $sheet->getStyle('A1:K4')->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ]
                ]);
            },
        ];
    }
}
