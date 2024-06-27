<?php //dd($ExcellDataArray);?>
<table>
    <thead>
        <tr>
            <th style="text-align: center; font-weight: bold;">Sr.No</th>
            <th style="text-align: center; font-weight: bold;">Firstname</th>
            <th style="text-align: center; font-weight: bold;">Lastname</th>
            <th style="text-align: center; font-weight: bold;">Email</th>
            <th style="text-align: center; font-weight: bold;">Registration ID</th>
            <th style="text-align: center; font-weight: bold;">Payu ID</th>
            <th style="text-align: center; font-weight: bold;">Booking Date</th>
            <th style="text-align: center; font-weight: bold;">Payment Status</th>
            <th style="text-align: center; font-weight: bold;">Convenience Fee</th>
            <th style="text-align: center; font-weight: bold;">Convenience Fee GST</th>
            <th style="text-align: center; font-weight: bold;">Payment Gateway GST</th>
            <th style="text-align: center; font-weight: bold;">Platform Charges GST</th>
            <th style="text-align: center; font-weight: bold;">Platform Charges</th>
            <th style="text-align: center; font-weight: bold;">Total Service Charges</th>
            <th style="text-align: center; font-weight: bold;">Final Organiser Amount</th>
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
                   <td style="text-align: left;"><?php echo $res->registration_id; ?></td>
                   <td style="text-align: left;"><?php echo $res->payu_id; ?></td>
                   <td style="text-align: left;"><?php echo $res->booking_date; ?></td>
                   <td style="text-align: left;"><?php echo $res->payment_status; ?></td>

                   <td style="text-align: right;"><?php echo $res->Convenience_fee; ?></td>
                   <td style="text-align: right;"><?php echo $res->cf_gst; ?></td>
                   <td style="text-align: right;"><?php echo $res->pg_gst; ?></td>
                   <td style="text-align: right;"><?php echo $res->platform_charges; ?></td>
                   <td style="text-align: right;"><?php echo $res->pc_gst; ?></td>
                   <td style="text-align: right;"><?php echo $res->total_service_charges; ?></td>
                   <td style="text-align: right;"><?php echo $res->final_organiser_amount; ?></td>
                </tr>
            <?php $i++; }} ?>

    </tbody>
</table>