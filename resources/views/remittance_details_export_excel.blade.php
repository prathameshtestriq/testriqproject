<?php //dd($ExcellDataArray);?>
<table>
    <thead>
        <tr>
            <th style="text-align: center; font-weight: bold;">Sr.No</th>
            <th style="text-align: center; font-weight: bold;">Firstname</th>
            <th style="text-align: center; font-weight: bold;">Lastname</th>
            <th style="text-align: center; font-weight: bold;">Email</th>
            <th style="text-align: center; font-weight: bold;">Mobile</th>
            <th style="text-align: center; font-weight: bold;">Event Category</th>
            <th style="text-align: center; font-weight: bold;">Transaction/Order ID</th>
            <th style="text-align: center; font-weight: bold;">Registration ID</th>
            <th style="text-align: center; font-weight: bold;">Payu ID</th>
            <th style="text-align: center; font-weight: bold;">Booking Date</th>
            <th style="text-align: center; font-weight: bold;">Payment Status</th>
            <th style="text-align: center; font-weight: bold;">GST - Inclusive/Exclusive</th>
            

            <th style="text-align: center; font-weight: bold;">Registration Price</th>
            <th style="text-align: center; font-weight: bold;">Count</th>
            <th style="text-align: center; font-weight: bold;">Registration Amount</th>
            <th style="text-align: center; font-weight: bold;">Registration Fee GST</th>

            <th style="text-align: center; font-weight: bold;">Applied Coupon Amount</th>
            
            <th style="text-align: center; font-weight: bold;">Additional Amount</th>
            <th style="text-align: center; font-weight: bold;">Additional Amount Payment Gateway Charges</th>
            <th style="text-align: center; font-weight: bold;">Additional Amount Payment Gateway GST (18%)</th>

            <th style="text-align: center; font-weight: bold;">Convenience Fee - Paid By</th>
            <th style="text-align: center; font-weight: bold;">Payment Gateway - Paid By</th>

            <th style="text-align: center; font-weight: bold;">Convenience Fee</th>
            <th style="text-align: center; font-weight: bold;">Convenience Fee GST (18%)</th>

            <th style="text-align: center; font-weight: bold;">Platform Fee</th>
            <th style="text-align: center; font-weight: bold;">Platform Fee GST (18%)</th>

            <th style="text-align: center; font-weight: bold;">Payment Gateway Charges (1.85%)</th>
            <th style="text-align: center; font-weight: bold;">Payment Gateway GST (18%)</th>

            <th style="text-align: center; font-weight: bold;">Organiser Amount</th>
            <th style="text-align: center; font-weight: bold;">Final Amount</th>


        </tr>
    </thead>
    <tbody>
            <?php
                $i=1;

                $total_Single_ticket_price = $total_Ticket_count = $total_Ticket_price = $total_Registration_Fee_GST = $total_Applied_Coupon_Amount = $total_Extra_amount = $total_Extra_amount_pg_charges = $total_Extra_amount_pg_GST = $total_Convenience_fee = $total_Convenience_Fee_GST = $total_Platform_fee = $total_Platform_Fee_GST = $total_Payment_gateway_charges = $total_Payment_Gateway_GST = $total_Organiser_amount = $total_Final_total_amount = 0;

                if(!empty($AttendeeDataArray)){
                    foreach ($AttendeeDataArray as $res){ 

                        $total_Single_ticket_price += $res->Single_ticket_price;
                        $total_Ticket_count += $res->Ticket_count;
                        $total_Ticket_price += $res->Ticket_price;
                        $total_Registration_Fee_GST += $res->Registration_Fee_GST;
                        $total_Applied_Coupon_Amount += $res->Applied_Coupon_Amount;
                        $total_Extra_amount += $res->Extra_amount;
                        $total_Extra_amount_pg_charges += $res->Extra_amount_pg_charges;
                        $total_Extra_amount_pg_GST += $res->Extra_amount_pg_GST;
                        $total_Convenience_fee += $res->Convenience_fee;
                        $total_Convenience_Fee_GST += $res->Convenience_Fee_GST;
                        $total_Platform_fee += $res->Platform_fee;
                        $total_Platform_Fee_GST += $res->Platform_Fee_GST;
                        $total_Payment_gateway_charges += $res->Payment_gateway_charges;
                        $total_Payment_Gateway_GST += $res->Payment_Gateway_GST;
                        $total_Organiser_amount += $res->Organiser_amount;
                        $total_Final_total_amount += $res->Final_total_amount;
 
            ?>
                
                <tr>
                   <td style="text-align: center;"><?php echo $i; ?></td>
                   <td style="text-align: left;"><?php echo $res->firstname; ?></td>
                   <td style="text-align: left;"><?php echo $res->lastname; ?></td>
                   <td style="text-align: left;"><?php echo $res->email; ?></td>
                   <td style="text-align: left;"><?php echo $res->mobile; ?></td>
                   <td style="text-align: left;"><?php echo $res->category_name; ?></td>
                   <td style="text-align: left;"><?php echo $res->transaction_id; ?></td>
                   <td style="text-align: left;"><?php echo $res->registration_id; ?></td>
                   <td style="text-align: left;"><?php echo $res->payu_id; ?></td>
                   <td style="text-align: left;"><?php echo $res->booking_date; ?></td>
                   <td style="text-align: left;"><?php echo $res->payment_status; ?></td>
                   <td style="text-align: left;"><?php echo $res->taxes_status; ?></td>

                   <td style="text-align: right;"><?php echo $res->Single_ticket_price; ?></td>
                   <td style="text-align: right;"><?php echo $res->Ticket_count; ?></td>
                   <td style="text-align: right;"><?php echo $res->Ticket_price; ?></td>
                   <td style="text-align: right;"><?php echo $res->Registration_Fee_GST; ?></td>
                   <td style="text-align: right;"><?php echo $res->Applied_Coupon_Amount; ?></td>

                   <td style="text-align: right;"><?php echo $res->Extra_amount; ?></td>
                   <td style="text-align: right;"><?php echo $res->Extra_amount_pg_charges; ?></td>
                   <td style="text-align: right;"><?php echo $res->Extra_amount_pg_GST; ?></td>

                   <td style="text-align: right;"><?php echo $res->Pass_Bare; ?></td>
                   <td style="text-align: right;"><?php echo $res->Pg_Bare; ?></td>

                   <td style="text-align: right;"><?php echo $res->Convenience_fee; ?></td>
                   <td style="text-align: right;"><?php echo $res->Convenience_Fee_GST; ?></td>

                   <td style="text-align: right;"><?php echo $res->Platform_fee; ?></td>
                   <td style="text-align: right;"><?php echo $res->Platform_Fee_GST; ?></td>

                   <td style="text-align: right;"><?php echo $res->Payment_gateway_charges; ?></td>
                   <td style="text-align: right;"><?php echo $res->Payment_Gateway_GST; ?></td>

                   <td style="text-align: right;"><?php echo $res->Organiser_amount; ?></td>
                   <td style="text-align: right;"><?php echo $res->Final_total_amount; ?></td>
                </tr>
            <?php $i++; }} ?>

            <tr>
                   <td style="text-align: center;">&nbsp;</td>
                   <td style="text-align: center;">&nbsp;</td>
                   <td style="text-align: center;">&nbsp;</td>
                   <td style="text-align: center;">&nbsp;</td>
                   <td style="text-align: center;">&nbsp;</td>
                   <td style="text-align: center;">&nbsp;</td>
                   <td style="text-align: center;">&nbsp;</td>
                   <td style="text-align: center;">&nbsp;</td>
                   <td style="text-align: center;">&nbsp;</td>
                   <td style="text-align: center;">&nbsp;</td>
                   <td style="text-align: center;">&nbsp;</td>
                   <td style="text-align: center;"><strong>Total</strong></td>

                   <td style="text-align: right;"><strong><?php echo $total_Single_ticket_price; ?></strong></td>
                   <td style="text-align: right;"><strong><?php echo $total_Ticket_count; ?></strong></td>
                   <td style="text-align: right;"><strong><?php echo $total_Ticket_price; ?></strong></td>
                   <td style="text-align: right;"><strong><?php echo $total_Registration_Fee_GST; ?></strong></td>
                   <td style="text-align: right;"><strong><?php echo $total_Applied_Coupon_Amount; ?></strong></td>

                   <td style="text-align: right;"><strong><?php echo $total_Extra_amount; ?></strong></td>
                   <td style="text-align: right;"><strong><?php echo $total_Extra_amount_pg_charges; ?></strong></td>
                   <td style="text-align: right;"><strong><?php echo $total_Extra_amount_pg_GST; ?></strong></td>

                   <td style="text-align: center;"><strong>&nbsp;</strong></td>
                   <td style="text-align: center;"><strong>&nbsp;</strong></td>

                   <td style="text-align: right;"><strong><?php echo $total_Convenience_fee; ?></strong></td>
                   <td style="text-align: right;"><strong><?php echo $total_Convenience_Fee_GST; ?></strong></td>

                   <td style="text-align: right;"><strong><?php echo $total_Platform_fee; ?></strong></td>
                   <td style="text-align: right;"><strong><?php echo $total_Platform_Fee_GST; ?></strong></td>

                   <td style="text-align: right;"><strong><?php echo $total_Payment_gateway_charges; ?></strong></td>
                   <td style="text-align: right;"><strong><?php echo $total_Payment_Gateway_GST; ?></strong></td>

                   <td style="text-align: right;"><strong><?php echo $total_Organiser_amount; ?></strong></td>
                   <td style="text-align: right;"><strong><?php echo $total_Final_total_amount; ?></strong></td>
            </tr>

    </tbody>
</table>

 