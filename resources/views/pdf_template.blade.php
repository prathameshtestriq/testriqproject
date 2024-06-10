<!-- <!DOCTYPE html>
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

</html> -->

<!DOCTYPE html>
<html>

<head>
    <title>Ticket PDF</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            /* background-color: #ed1c24; */
        }

        .container {
            /* width: 210mm; */
            /* height: 297mm; */
            height: 273mm;
            /* padding: 10mm; */
            background: white;
            margin: auto;
            box-sizing: border-box;
        }

        h1 {
            font-size: 20px;
            color: #333;
        }

        p {
            font-size: 14px;
            color: #555;
            line-height: 1.5;
            margin: 10px 0;
        }


        .qr-code {
            text-align: center;
        }

        .qr-code img {
            height: 140px;
            width: 140px;
        }



        .event-details p,
        .registration-details p {
            margin: 5px 0;
        }

        .event-link a {
            color: #0066cc;
            text-decoration: none;
        }

        .ytcr-banner {
            text-align: center;
        }

        .ytcr-banner img {
            width: auto;
            height: 50px;
            padding: 10px 10px;
        }


        .main-banner {
            position: relative;
            padding: 10px 10px;
            border-radius: 5px;
            text-align: center;
            background-image: url('eventimg.jpg');
            background-repeat: no-repeat;
            background-size: cover;
            background-position: center;
            /* background-color: red; */
            color: white;
            /* Optional: To ensure the text is readable */
            margin: 20px 0px;
            height: 100px;
        }

        .main-banner::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 120px;
            background-color: rgba(0, 0, 0, 0.7);
            /* 50% dark overlay */
            border-radius: 5px;
            /* Match the border-radius of the parent */
            z-index: 0;
            /* Place the overlay behind the text */
        }



        .main-banner h1,
        .main-banner p {
            position: relative;
            z-index: 1;
            /* Place the text above the overlay */
        }


        .event-name {
            margin-bottom: 0px;
            color: white;
            font-size: 25px;
        }

        .event-organizer {
            color: white;
        }

        .booking-conformation {
            padding: 30px 5px;
            text-align: center;
        }

        .booking-conformation .title {
            margin-top: 0;
        }

        .booking-conformation hr {
            width: 400px;
            padding: 1px;
            background: #ed1c24;
            border: #ed1c24;
        }

        .booking-conformation .booking-number {
            margin-bottom: 0;
        }

        .booking-conformation .booking-number span {
            color: #0066cc;
        }



        .event-details table {
            width: 100%;
            border-collapse: collapse;

            color: #393939;
        }

        .event-details table th {
            text-align: left;
            color: #212121;
            padding-top: .8em;
            padding-bottom: .8em;
            border: 1px solid gray;
            border-left: none;
            border-right: none;
            border-top: none;
        }

        .event-details table td {
            padding-top: .8em;
            padding-bottom: .8em;

        }
    </style>
</head>

<body>

    <div class="container">
        <div class="ytcr-banner">
            <a><img src="ytcr-logo.png" alt="ytcr-logo"></a>
        </div>


        <div class="main-banner">
            <h1 class="event-name">{{ $ticket_details['TicketName']}}</h1>
            <p class="event-organizer">{{ $org_details->name }}</p>
        </div>


        <div class="event-details">
            <table>
                <tbody>
                    <tr>
                        <th colspan="2">Applicant Details</th>
                    </tr>
                    <tr>
                        <td>Name</td>
                        <td>{{ $Username }}</td>
                    </tr>
                    <tr>
                        <td>other info</td>
                        <td>NA</td>
                    </tr>
                    <tr>
                        <th colspan="2">Event Details</th>
                    </tr>
                    <tr>
                        <td>Organizer</td>
                        <td>{{ $org_details->name }}</td>
                    </tr>
                    <tr>
                        <td>Venue</td>
                        <td>{{ $event_details->Venue }}</td>
                    </tr>
                    <tr>
                        <td>Event Date &amp; Time</td>
                        <td>{{ $event_details->start_date }} : {{ $event_details->start_time_event }} -
                            {{ $event_details->end_date }} : {{ $event_details->end_date_event }}
                        </td>
                    </tr>
                    <!-- <tr>
                  <td>Event End</td>
                  <td>{{ $event_details->end_date }} : {{ $event_details->end_date_event }}</td>
                </tr> -->
                    <tr>
                        <td>Event Link</td>
                        <td><a>{{ $EventLink }}</a></td>
                    </tr>
                    <tr>
                        <th colspan="2">Booking Details</th>
                    </tr>
                    <tr>
                        <td>Booking Date &amp; Time</td>
                        <td>{{ $ticket_details['booking_start_date'] }} {{ $ticket_details['booking_time'] }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="booking-conformation">
            <h4 class="title">Booking Conformation</h4>
            <hr>
            <h3 class="booking-number">Booking Number : {{ $ticket_details['unique_ticket_id'] }}</h3>

        </div>

        <div class="qr-code">
            <img src="data:image/png;base64,{{ $QrCode }}" alt="qr-img">
        </div>
    </div>
</body>

</html>