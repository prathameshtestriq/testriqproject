<!DOCTYPE html>
<html>

<head>
    <title>Ticket PDF</title>
</head>

<body>
    <h1>Hi {{ $Username }},<br />
        Thank you for purchasing {{ $ticket_details['quantity'] }} ticket(s) for {{ $ticket_details['TicketName'] }}
    </h1>
    <p>Your Registration details are as follows:<br />
        Booking Id : {{ $ticket_details['unique_ticket_id'] }}<br />
        Booking Date : {{ $ticket_details['booking_start_date'] }} {{ $ticket_details['booking_time'] }}</p>

    <p>
        Event Details :<br />
        Organizer : {{ $org_details->name }}<br />
        Venue : {{ $event_details->Venue }}<br />
        Event Date : {{ $event_details->start_date }} : {{ $event_details->start_time_event }} -
        {{ $event_details->end_date }} : {{ $event_details->end_date_event }}<br />
        Event Link : {{ $EventLink }}<br />
    </p>

    <div>
        <img src="data:image/png;base64,{{ $QrCode }}" alt="QR Code" style="height:100px;width:100px">
    </div>
</body>

</html>