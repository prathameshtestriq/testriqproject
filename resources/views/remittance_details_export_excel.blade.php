<?php //dd($ExcellDataArray);?>
<table>
    <thead>
        <tr>
            <th style="text-align: center; font-weight: bold;">Sr.No</th>
            <th style="text-align: left; font-weight: bold;">Firstname</th>
            <th style="text-align: left; font-weight: bold;">Lastname</th>
            <th style="text-align: left; font-weight: bold;">Email</th>
            <th style="text-align: left; font-weight: bold;">Mobile</th>
            <th style="text-align: left; font-weight: bold;">Bulk Upload Group Name</th>
            <th style="text-align: left; font-weight: bold;">Event Category</th>
            <th style="text-align: left; font-weight: bold;">Type or Registration</th>
            <th style="text-align: left; font-weight: bold;">Transaction/Order ID</th>
            <th style="text-align: left; font-weight: bold;">Registration ID</th>
            <th style="text-align: left; font-weight: bold;">Payu ID</th>
            <th style="text-align: left; font-weight: bold;">Registration Date</th>
            <th style="text-align: left; font-weight: bold;">Payment Status</th>
            <th style="text-align: left; font-weight: bold;">GST - Inclusive/Exclusive</th>
            

            <!-- <th style="text-align: center; font-weight: bold;">Registration Price</th> -->
            <th style="text-align: right; font-weight: bold;">Count</th>
            <!-- <th style="text-align: right; font-weight: bold;">Registration Amount Paid</th> -->
            <th style="text-align: right; font-weight: bold;">Ticket Amount</th>
            <th style="text-align: right; font-weight: bold;">Registration Fee GST</th>

            <th style="text-align: right; font-weight: bold;">Applied Coupon Amount</th>
            
            <th style="text-align: right; font-weight: bold;">Additional Amount</th>
            <th style="text-align: right; font-weight: bold;">Additional Amount Payment Gateway Charges</th>
            <th style="text-align: right; font-weight: bold;">Additional Amount Payment Gateway GST (18%)</th>

            <th style="text-align: left; font-weight: bold;">Convenience Fee - Paid By</th>
            <th style="text-align: left; font-weight: bold;">Payment Gateway - Paid By</th>

            <th style="text-align: right; font-weight: bold;">Convenience Fee</th>
            <th style="text-align: right; font-weight: bold;">Convenience Fee GST (18%)</th>

            <th style="text-align: right; font-weight: bold;">Platform Fee</th>
            <th style="text-align: right; font-weight: bold;">Platform Fee GST (18%)</th>

            <th style="text-align: right; font-weight: bold;">Payment Gateway Charges (1.85%)</th>
            <th style="text-align: right; font-weight: bold;">Payment Gateway GST (18%)</th>

            <th style="text-align: right; font-weight: bold;">Organiser Amount</th>
            <th style="text-align: right; font-weight: bold;">Final Registration Amount</th>


        </tr>
    </thead>
    <tbody>
            <?php
                $i=1;

                $total_Single_ticket_price = $total_Ticket_count = $total_Ticket_price = $total_Registration_Fee_GST = $total_Applied_Coupon_Amount = $total_Extra_amount = $total_Extra_amount_pg_charges = $total_Extra_amount_pg_GST = $total_Convenience_fee = $total_Convenience_Fee_GST = $total_Platform_fee = $total_Platform_Fee_GST = $total_Payment_gateway_charges = $total_Payment_Gateway_GST = $total_Organiser_amount = $total_Final_total_amount = 0;

                if(!empty($AttendeeDataArray)){
                    foreach ($AttendeeDataArray as $res){ 

                        // $total_Single_ticket_price += $res->Single_ticket_price;
                        $total_Ticket_count += isset($res->Ticket_count) && !empty($res->Ticket_count) ? $res->Ticket_count : 0;
                        $total_Ticket_price += isset($res->Ticket_price) && !empty($res->Ticket_price) ? $res->Ticket_price : 0;
                        $total_Registration_Fee_GST += isset($res->Registration_Fee_GST) && !empty($res->Registration_Fee_GST) ? $res->Registration_Fee_GST : 0;
                        $total_Applied_Coupon_Amount += isset($res->Applied_Coupon_Amount) && !empty($res->Applied_Coupon_Amount) ? $res->Applied_Coupon_Amount : 0;
                        $total_Extra_amount += isset($res->Extra_amount) && !empty($res->Extra_amount) ? $res->Extra_amount : 0;
                        $total_Extra_amount_pg_charges += isset($res->Extra_amount_pg_charges) && !empty($res->Extra_amount_pg_charges) ? $res->Extra_amount_pg_charges : 0;
                        $total_Extra_amount_pg_GST += isset($res->Extra_amount_pg_GST) && !empty($res->Extra_amount_pg_GST) ? $res->Extra_amount_pg_GST : 0;
                        $total_Convenience_fee += isset($res->Convenience_fee) && !empty($res->Convenience_fee) ? $res->Convenience_fee : 0;
                        $total_Convenience_Fee_GST += isset($res->Convenience_Fee_GST) && !empty($res->Convenience_Fee_GST) ? $res->Convenience_Fee_GST : 0;
                        $total_Platform_fee += isset($res->Platform_fee) && !empty($res->Platform_fee) ? $res->Platform_fee : 0;
                        $total_Platform_Fee_GST += isset($res->Platform_Fee_GST) && !empty($res->Platform_Fee_GST) ? $res->Platform_Fee_GST : 0;
                        $total_Payment_gateway_charges += isset($res->Payment_gateway_charges) && !empty($res->Payment_gateway_charges) ? $res->Payment_gateway_charges : 0;
                        $total_Payment_Gateway_GST += isset($res->Payment_Gateway_GST) && !empty($res->Payment_Gateway_GST) ? $res->Payment_Gateway_GST : 0;
                        $total_Organiser_amount += isset($res->Organiser_amount) && !empty($res->Organiser_amount) ? $res->Organiser_amount : 0;
                        $total_Final_total_amount += isset($res->Final_total_amount) && !empty($res->Final_total_amount) ? $res->Final_total_amount : 0;
 
            ?>
                
                <tr>
                   <td style="text-align: center;"><?php echo $i; ?></td>
                   <td style="text-align: left;"><?php echo $res->firstname; ?></td>
                   <td style="text-align: left;"><?php echo $res->lastname; ?></td>
                   <td style="text-align: left;"><?php echo $res->email; ?></td>
                   <td style="text-align: left;"><?php echo $res->mobile; ?></td>
                   <td style="text-align: left;"><?php echo $res->bulk_upload_group_name; ?></td>
                   <td style="text-align: left;"><?php echo $res->category_name; ?></td>
                   <td style="text-align: left;"><?php echo $res->category_type; ?></td>
                   <td style="text-align: left;"><?php echo $res->transaction_id; ?></td>
                   <td style="text-align: left;"><?php echo $res->registration_id; ?></td>
                   <td style="text-align: left;"><?php echo $res->payu_id; ?></td>
                   <td style="text-align: left;"><?php echo $res->booking_date; ?></td>
                   <td style="text-align: left;"><?php echo $res->payment_status; ?></td>
                   <td style="text-align: left;"><?php echo $res->taxes_status; ?></td>

                   <td style="text-align: right;"><?php echo isset($res->Ticket_count) && !empty($res->Ticket_count) ? $res->Ticket_count : 0 ; ?></td>
                   <td style="text-align: right;"><?php echo isset($res->Ticket_price) && !empty($res->Ticket_price) ? number_format($res->Ticket_price,2) : 0 ; ?></td>
                   <td style="text-align: right;"><?php echo isset($res->Registration_Fee_GST) && !empty($res->Registration_Fee_GST) ? $res->Registration_Fee_GST : 0 ; ?></td>
                   <td style="text-align: right;"><?php echo isset($res->Applied_Coupon_Amount) && !empty($res->Applied_Coupon_Amount) ? number_format($res->Applied_Coupon_Amount,2) : 0 ; ?></td>
                   <td style="text-align: right;"><?php echo isset($res->Extra_amount) && !empty($res->Extra_amount) ? number_format($res->Extra_amount,2) : 0 ; ?></td>
                   <td style="text-align: right;"><?php echo isset($res->Extra_amount_pg_charges) && !empty($res->Extra_amount_pg_charges) ? $res->Extra_amount_pg_charges : 0 ; ?></td>
                   <td style="text-align: right;"><?php echo isset($res->Extra_amount_pg_GST) && !empty($res->Extra_amount_pg_GST) ? $res->Extra_amount_pg_GST : 0 ; ?></td>

                   <td style="text-align: left;"><?php echo $res->Pass_Bare; ?></td>
                   <td style="text-align: left;"><?php echo $res->Pg_Bare; ?></td>

                   <td style="text-align: right;"><?php echo isset($res->Convenience_fee) && !empty($res->Convenience_fee) ? number_format($res->Convenience_fee,2) : 0 ; ?></td>
                   <td style="text-align: right;"><?php echo isset($res->Convenience_Fee_GST) && !empty($res->Convenience_Fee_GST) ? number_format($res->Convenience_Fee_GST,2) : 0 ; ?></td>

                   <td style="text-align: right;"><?php echo isset($res->Platform_fee) && !empty($res->Platform_fee) ? number_format($res->Platform_fee,2) : 0 ; ?></td>
                   <td style="text-align: right;"><?php echo isset($res->Platform_Fee_GST) && !empty($res->Platform_Fee_GST) ? number_format($res->Platform_Fee_GST,2) : 0 ; ?></td>

                   <td style="text-align: right;"><?php echo isset($res->Payment_gateway_charges) && !empty($res->Payment_gateway_charges) ? number_format($res->Payment_gateway_charges,2) : 0 ; ?></td>
                   <td style="text-align: right;"><?php echo isset($res->Payment_Gateway_GST) && !empty($res->Payment_Gateway_GST) ? number_format($res->Payment_Gateway_GST,2) : 0 ; ?></td>

                   <td style="text-align: right;"><?php echo isset($res->Organiser_amount) && !empty($res->Organiser_amount) ? number_format($res->Organiser_amount,2) : 0 ; ?></td>
                   <td style="text-align: right;"><?php echo isset($res->Final_total_amount) && !empty($res->Final_total_amount) ? number_format($res->Final_total_amount,2) : 0 ; ?></td>

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
                   <td style="text-align: center;">&nbsp;</td>
                   <td style="text-align: center;">&nbsp;</td>
                   <td style="text-align: center;"><strong>Total</strong></td>

                   <!-- <td style="text-align: right;"><strong><?php //echo $total_Single_ticket_price; ?></strong></td> -->
                   <td style="text-align: right;"><strong><?php echo $total_Ticket_count; ?></strong></td>
                   <td style="text-align: right;"><strong><?php echo number_format($total_Ticket_price,2); ?></strong></td>
                   <td style="text-align: right;"><strong><?php echo $total_Registration_Fee_GST; ?></strong></td>
                   <td style="text-align: right;"><strong><?php echo number_format($total_Applied_Coupon_Amount,2); ?></strong></td>

                   <td style="text-align: right;"><strong><?php echo number_format($total_Extra_amount,2); ?></strong></td>
                   <td style="text-align: right;"><strong><?php echo number_format($total_Extra_amount_pg_charges,2); ?></strong></td>
                   <td style="text-align: right;"><strong><?php echo number_format($total_Extra_amount_pg_GST,2); ?></strong></td>

                   <td style="text-align: center;"><strong>&nbsp;</strong></td>
                   <td style="text-align: center;"><strong>&nbsp;</strong></td>

                   <td style="text-align: right;"><strong><?php echo number_format($total_Convenience_fee,2); ?></strong></td>
                   <td style="text-align: right;"><strong><?php echo number_format($total_Convenience_Fee_GST,2); ?></strong></td>

                   <td style="text-align: right;"><strong><?php echo number_format($total_Platform_fee,2); ?></strong></td>
                   <td style="text-align: right;"><strong><?php echo number_format($total_Platform_Fee_GST,2); ?></strong></td>

                   <td style="text-align: right;"><strong><?php echo number_format($total_Payment_gateway_charges,2); ?></strong></td>
                   <td style="text-align: right;"><strong><?php echo number_format($total_Payment_Gateway_GST,2); ?></strong></td>

                   <td style="text-align: right;"><strong><?php echo number_format($total_Organiser_amount,2); ?></strong></td>
                   <td style="text-align: right;"><strong><?php echo number_format($total_Final_total_amount,2); ?></strong></td>
            </tr>

    </tbody>
</table>

 