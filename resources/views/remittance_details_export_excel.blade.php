<?php //dd($ExcellDataArray);?>
<table>
    <thead>
        <tr>
            <th style="text-align: center; font-weight: bold;">Sr.No</th>
            <th style="text-align: center; font-weight: bold;">Firstname</th>
            <th style="text-align: center; font-weight: bold;">Lastname</th>
            <th style="text-align: center; font-weight: bold;">Email</th>
            <th style="text-align: center; font-weight: bold;">Transaction/Order ID</th>
            <th style="text-align: center; font-weight: bold;">Registration ID</th>
            <th style="text-align: center; font-weight: bold;">Payu ID</th>
            <th style="text-align: center; font-weight: bold;">Booking Date</th>
            <th style="text-align: center; font-weight: bold;">Payment Status</th>

            <th style="text-align: center; font-weight: bold;">Registration Price</th>
            <th style="text-align: center; font-weight: bold;">Count</th>
            <th style="text-align: center; font-weight: bold;">Registration Amount</th>
            <th style="text-align: center; font-weight: bold;">Registration Fee GST</th>

            <th style="text-align: center; font-weight: bold;">Applied Coupon Amount</th>
            
            <th style="text-align: center; font-weight: bold;">Additional Amount</th>
            <th style="text-align: center; font-weight: bold;">Additional Amount Payment Gateway Charges</th>
            <th style="text-align: center; font-weight: bold;">Additional Amount Payment Gateway GST (18%)</th>

            <th style="text-align: center; font-weight: bold;">Convenience Fee</th>
            <th style="text-align: center; font-weight: bold;">Convenience Fee GST (18%)</th>

            <th style="text-align: center; font-weight: bold;">Platform Fee</th>
            <th style="text-align: center; font-weight: bold;">Platform Fee GST (18%)</th>

            <th style="text-align: center; font-weight: bold;">Payment Gateway Charges (1.85%)</th>
            <th style="text-align: center; font-weight: bold;">Payment Gateway GST (18%)</th>

            <th style="text-align: center; font-weight: bold;">Final Amount</th>


        </tr>
    </thead>
    <tbody>
            <?php
                $i=1;
                if(!empty($AttendeeDataArray)){
                    foreach ($AttendeeDataArray as $res){ ?>
                
                <tr>
                   <td style="text-align: center;"><?php echo $i; ?></td>
                   <td style="text-align: left;"><?php echo $res->firstname; ?></td>
                   <td style="text-align: left;"><?php echo $res->lastname; ?></td>
                   <td style="text-align: left;"><?php echo $res->email; ?></td>
                   <td style="text-align: left;"><?php echo $res->transaction_id; ?></td>
                   <td style="text-align: left;"><?php echo $res->registration_id; ?></td>
                   <td style="text-align: left;"><?php echo $res->payu_id; ?></td>
                   <td style="text-align: left;"><?php echo $res->booking_date; ?></td>
                   <td style="text-align: left;"><?php echo $res->payment_status; ?></td>

                   <td style="text-align: right;"><?php echo $res->Single_ticket_price; ?></td>
                   <td style="text-align: right;"><?php echo $res->Ticket_count; ?></td>
                   <td style="text-align: right;"><?php echo $res->Ticket_price; ?></td>
                   <td style="text-align: right;"><?php echo $res->Registration_Fee_GST; ?></td>
                   <td style="text-align: right;"><?php echo $res->Applied_Coupon_Amount; ?></td>

                   <td style="text-align: right;"><?php echo $res->Extra_amount; ?></td>
                   <td style="text-align: right;"><?php echo $res->Extra_amount_pg_charges; ?></td>
                   <td style="text-align: right;"><?php echo $res->Extra_amount_pg_GST; ?></td>

                   <td style="text-align: right;"><?php echo $res->Convenience_fee; ?></td>
                   <td style="text-align: right;"><?php echo $res->Convenience_Fee_GST; ?></td>

                   <td style="text-align: right;"><?php echo $res->Platform_fee; ?></td>
                   <td style="text-align: right;"><?php echo $res->Platform_Fee_GST; ?></td>

                   <td style="text-align: right;"><?php echo $res->Payment_gateway_charges; ?></td>
                   <td style="text-align: right;"><?php echo $res->Payment_Gateway_GST; ?></td>

                   <td style="text-align: right;"><?php echo $res->Final_total_amount; ?></td>
                </tr>
            <?php $i++; }} ?>

    </tbody>
</table>

 