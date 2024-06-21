<!DOCTYPE html>


<html>

<head>
    <meta charset="UTF-8">
    <title>Races Registration</title>
    <!-- <link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/img/ico/favicon.ico') }}"> -->
    <link rel="shortcut icon" type="image/x-icon" href="https://racesregistrations.com/assets/img/favicon.ico">

    <style>
        @font-face {
            font-family: 'DejaVu Sans';
            src: url('{{ resource_path('fonts/DejaVuSans.ttf') }}') format('truetype');
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
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
            <h1 class="event-name">{{ isset($event_details->name) ? $event_details->name : ""}}</h1>
            <p class="event-organizer">{{ isset($ticket_details['TicketName']) ? $ticket_details['TicketName'] : "" }}
            </p>
        </div>

        <div class="event-details">
            <table>
                <tbody>
                    <tr>
                        <th colspan="2">Participant Details</th>
                    </tr>

                    <tr>
                        <td>Name</td>
                        <td>{{ isset($ticket_details["firstname"]) ? $ticket_details["firstname"] . " " . $ticket_details["lastname"] : "" }}
                        </td>
                    </tr>
                    <tr>
                        <td>Email</td>
                        <td>{{ isset($ticket_details["email"]) ? $ticket_details["email"] : "" }}</td>
                    </tr>
                    <tr>
                        <th colspan="2">Event Details</th>
                    </tr>
                    <tr>
                        <td>Organiser</td>
                        <td>{{ isset($org_details->name) ? $org_details->name : '' }}</td>
                    </tr>

                    <tr>
                        <td>Venue</td>
                        <td>{{ isset($event_details->Venue) ? $event_details->Venue : '' }}</td>
                    </tr>
                    <tr>
                        <td>Event Starts On</td>
                        <td>{{ isset($event_details->start_date) ? $event_details->start_date : '' }} :
                            {{isset($event_details->start_time_event) ? $event_details->start_time_event : ''  }}
                        </td>
                    </tr>
                    <tr>
                        <td>Event Ends On</td>
                        <td> {{isset($event_details->end_date) ? $event_details->end_date : ''   }} :
                            {{isset($event_details->end_date_event) ? $event_details->end_date_event : ''  }}
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
                        <th colspan="2">Registration Details</th>
                    </tr>
                    <tr>
                        <td>Registered By</td>
                        <td>{{ isset($user_details->username) ? $user_details->username : "" }}</td>
                    </tr>

                    <tr>
                        <td>Email</td>
                        <td>{{ isset($user_details->email) ? $user_details->email : "" }}</td>
                    </tr>

                    <tr>
                        <td>Mobile</td>
                        <td>{{ isset($user_details->mobile) ? $user_details->mobile : "" }}</td>
                    </tr>

                    <tr>
                        <td>Registration Date &amp; Time</td>
                        <td>{{(!empty($ticket_details["booking_date"]))? date("d M Y h:i A", $ticket_details["booking_date"]) : ""}}
                        </td>
                    </tr>

                    <tr>
                        <td>Amount</td>
                        <td>
                            <span style="font-family:dejavusans;">&#8377;</span>
                            {{ isset($ticket_details['ticket_amount']) ? $ticket_details['ticket_amount'] : "" }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <br />
        <br />
        <br />
        <br />
        <br />
        <?php if (isset($extra_details) && count($extra_details) > 0) { ?>
        <div class="event-details">
            <h4 class="title">Additional Purchases</h4>
            <hr>
            <table class="table table-bordered">
                <tbody>
                    <?php    foreach ($extra_details as $extra_detail) {
        $actual_value = intval($extra_detail->ActualValue);
        $question_form_option = json_decode($extra_detail->question_form_option, true);

        $label = null;
        foreach ($question_form_option as $option) {
            if ($option['id'] === $actual_value) {
                $label = $option['label'];
                break;
            }
        }
                                ?>
                    <tr>
                        <td>{{$extra_detail->question_label}}</td>
                        <td>{{ $label }}</td>
                    </tr>
                    <?php    }  ?>
                </tbody>
            </table>
        </div>
        <?php    }?>
        <br />
        <br />
        <br />
        <br />
        <br />


        <div class="booking-conformation">
            <h4 class="title">Registration Confirmation</h4>
            <hr>
            <h3 class="booking-number">Registration Number :
                {{ isset($ticket_details['unique_ticket_id']) ? $ticket_details['unique_ticket_id'] : "" }}
            </h3>
        </div>

        <div class="qr-code">
            <img src="data:image/png;base64,{{ $QrCode }}" alt="qr-img">
        </div>
    </div>
</body>

</html>