<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Event extends Model
{
    use HasFactory;

    protected $table = 'events';
    public $timestamps = false;

    public static function get($aSearch)
    {
        $query = Event::from('events as e');
        $query->where('e.deleted', 0);

        return $query;
    }

    public function isFollowed($EventId, $UserId)
    {
        // dd($EventId,$UserId);
        $Follow = 0;
        if (!empty($EventId) && !empty($UserId)) {
            // dd($EventId,$UserId);
            $sql = "SELECT id FROM event_user_follow WHERE event_id=:event_id AND user_id=:user_id";
            $Result = DB::select($sql, array('event_id' => $EventId, 'user_id' => $UserId));
            // dd($Result);
            if (count($Result) > 0)
                $Follow = 1;
        }
        return $Follow;
    }

    public function isOrgFollowed($OrgId, $UserId)
    {
        // dd($OrgId,$UserId);
        $Follow = 0;
        if (!empty($OrgId) && !empty($UserId)) {
            // dd($EventId,$UserId);
            $sql = "SELECT id FROM organizers_follow WHERE organizer_id=:organizer_id AND user_id=:user_id";
            $Result = DB::select($sql, array('organizer_id' => $OrgId, 'user_id' => $UserId));
            // dd($Result);
            if (count($Result) > 0)
                $Follow = 1;
        }
        return $Follow;
    }

    function getCategory($EventId)
    {
        $sql = "SELECT c.* FROM category AS c WHERE active=1";
        $Allcategory = DB::select($sql);

        foreach ($Allcategory as $key => $value) {
            $sql = "SELECT id FROM event_category WHERE category_id=:category_id AND event_id=:event_id";
            $IsExist = DB::select($sql, array('category_id' => $value->id, 'event_id' => $EventId));
            $value->checked = (sizeof($IsExist) > 0) ? true : false;
        }
        return $Allcategory;
    }


    function getTypes($EventId)
    {
        $sql = "SELECT e.* FROM eTypes AS e WHERE e.active=1";
        $AllEventTypes = DB::select($sql);

        foreach ($AllEventTypes as $key => $value) {
            $sql = "SELECT id FROM event_type WHERE type_id=:type_id AND event_id=:event_id";
            $IsExist = DB::select($sql, array('type_id' => $value->id, 'event_id' => $EventId));
            $value->checked = (sizeof($IsExist) > 0) ? true : false;

            $value->logo = (isset($value->logo) && !empty($value->logo)) ? url('/') . '/assets/img/banner/' . $value->logo : "";
        }
        return $AllEventTypes;
    }

    function getDistances($EventId)
    {
        $SQL = "SELECT DISTINCT(t.category),(SELECT name FROM eTypes WHERE active=1 AND id=t.category) AS distance_name FROM event_tickets AS t WHERE t.event_id=:event_id AND t.active = 1 AND t.is_deleted = 0 AND t.category!=0";
        $Tickets = DB::select($SQL, array('event_id' => $EventId));
        return $Tickets;
    }


    public function getCategoryDetails($EventId)
    {
        $Return = [];
        if (!empty($EventId)) {
            $sql = "SELECT c.id,c.name,c.logo FROM category AS c
            LEFT JOIN event_category AS ec ON c.id = ec.category_id
            WHERE ec.event_id=:event_id";
            $Return = DB::select($sql, array('event_id' => $EventId));
        }
        return $Return;
    }

    public function getTypeDetails($EventId)
    {
        $Return = [];
        if (!empty($EventId)) {
            $sql = "SELECT c.id,c.name,c.logo FROM eTypes AS c
            LEFT JOIN event_type AS ec ON c.id = ec.type_id
            WHERE ec.event_id=:event_id";
            $Return = DB::select($sql, array('event_id' => $EventId));
        }
        return $Return;
    }

    public function getEventImages($EventId)
    {
        $sql = "SELECT id,image FROM event_images WHERE event_id=:event_id";
        $Result = DB::select($sql, array('event_id' => $EventId));
        // dd($Result);
        foreach ($Result as $item) {
            $item->image = !empty($item->image) ? url('/') . '/uploads/event_images/' . $item->image : "";
        }
        return $Result;
    }

    function getEventCount($CityId)
    {
        $EventCount = 0;
        $sql = 'SELECT COUNT(id) AS count FROM events WHERE city=:city AND active=1 AND deleted=0 AND event_info_status=1';
        $Count = DB::select($sql, array('city' => $CityId));
        if (sizeof($Count) > 0) {
            $EventCount = isset($Count[0]->count) ? $Count[0]->count : 0;
        }
        // dd($Count);
        return $EventCount;
    }

    function getEventTicketDetails($EventId)
    {
        // dd($EventId);
        $now = strtotime("now");
        // $now = strtotime(date('Y-m-d h:i:s'));
       // dd($now);
        $sSQL = 'SELECT * FROM event_tickets WHERE event_id = :event_id AND active = 1 AND is_deleted = 0 AND ticket_sale_start_date <= :now_start AND ticket_sale_end_date >= :now_end';
        $event_tickets = DB::select($sSQL, array('event_id' => $EventId, 'now_start' => $now, 'now_end' => $now));
        // dd($event_tickets);

        foreach ($event_tickets as $value) {
            // if ($value->ticket_status == 1) {

                $value->display_ticket_name = !empty($value->ticket_name) ? (strlen($value->ticket_name) > 40 ? ucwords(substr($value->ticket_name, 0, 80)) . "..." : ucwords($value->ticket_name)) : "";

                $sql = "SELECT COUNT(id) AS TotalBookedTickets FROM booking_details WHERE event_id=:event_id AND ticket_id=:ticket_id";
                $TotalTickets = DB::select($sql, array("event_id" => $EventId, "ticket_id" => $value->id));

                $value->TotalBookedTickets = ((sizeof($TotalTickets) > 0) && (isset($TotalTickets[0]->TotalBookedTickets))) ? $TotalTickets[0]->TotalBookedTickets : 0;
                $value->show_early_bird = 0;
                if ($value->early_bird == 1 && $value->TotalBookedTickets <= $value->no_of_tickets && $value->start_time <= $now && $value->end_time >= $now) {
                    $value->show_early_bird = 1;
                    $value->strike_out_price = ($value->early_bird == 1) ? $value->ticket_price : 0;

                    $discount_ticket_price = 0;
                    $total_discount = 0;
                    if ($value->discount === 1) { //percentage
                        $total_discount = ($value->ticket_price * ($value->discount_value / 100));
                        $discount_ticket_price = $value->ticket_price - $total_discount;
                    } else if ($value->discount === 2) { //amount
                        $total_discount = $value->discount_value;
                        $discount_ticket_price = $value->ticket_price - $value->discount_value;
                    }
                    $value->discount_ticket_price = $discount_ticket_price;
                    $value->total_discount = $total_discount;
                }
            // }
        }
        // if($EventId == 5) 
        // dd($event_tickets);
        $minMaxPrices = $this->getMinMaxTicketPrices($event_tickets);
        // dd($minMaxPrices);
        return $minMaxPrices;
    }

    function getMinMaxTicketPrices($ticketArray)
    {
        $minPrice = null;
        $maxPrice = null;
        $minPriceElement = null;
        $maxPriceElement = null;
        $minPriceTicketId = null;
        $maxPriceTicketId = null;

        foreach ($ticketArray as $ticket) {
            $ticketPrice = $ticket->ticket_price;
            $ticketId = $ticket->id;

            if ($minPrice === null || $ticketPrice < $minPrice) {
                $minPrice = $ticketPrice;
                $minPriceTicketId = $ticketId;
                $minPriceElement = $ticket;
            }

            if ($maxPrice === null || $ticketPrice > $maxPrice) {
                $maxPrice = $ticketPrice;
                $maxPriceTicketId = $ticketId;
                $maxPriceElement = $ticket;
            }
        }

        // Check if min_price and max_price are the same
        if ($minPriceElement != null && $maxPriceElement != null) {
            if ($minPriceTicketId === $maxPriceTicketId) {
                return [0 => $minPriceElement];
            } else {
                return [
                    0 => $minPriceElement,
                    1 => $maxPriceElement
                ];
            }
        } else {
            return [];
        }
    }

}
