<?php 
//print_r($_REQUEST); die;
//header("Location: http://localhost:3000/payment_gateway");

//-------------------
// Database connection parameters
$flag = 1; // prime - 1 / live - 2 / local - 3 

if($flag == 1){                   // prime
    $servername = 'localhost'; 
    $username   = 'root'; 
    $password   = 'pf4hP$v3g^xD'; 
    $database   = 'Races2.0_Web'; 
}else if($flag == 2){             // live
    $servername = 'localhost'; 
    $username   = 'newroot'; 
    $password   = 'ytcr@Swt12'; 
    $database   = 'Races2.0_Web'; 
}else{                            // local
    $servername = 'localhost'; 
    $username   = 'root'; 
    $password   = '12345'; 
    $database   = 'Races2.0_Web'; 
}

$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

    // Transcation data for insertion
    	$mihpayid = $_POST['mihpayid'];
        $mode = $_POST['mode'];
        $status=$_POST['status'];
        $unmappedstatus=$_POST['unmappedstatus'];
        $key =$_POST['key'];
        $txnid=$_POST['txnid'];
        $amount = $_POST['amount'];
        $discount=$_POST['discount'];
        $net_amount_debit=$_POST['net_amount_debit'];
        $addedon=$_POST['addedon'];
        $productinfo=$_POST['productinfo'];
        $firstname=$_POST['firstname'];
        $lastname=$_POST['lastname'];
        $address1=$_POST['address1'];
        $address2=$_POST['address2'];
        $city =$_POST['city'];
        $state=$_POST['state'];
        $country=$_POST['country'];
        $zipcode=$_POST['zipcode'];
        $email=$_POST['email'];
        $phone=$_POST['phone'];
        $udf1=$_POST['udf1'];
        $udf2=$_POST['udf2'];
        $udf3=$_POST['udf3'];
        $udf4=$_POST['udf4'];
        $udf5=$_POST['udf5'];
        $udf6=$_POST['udf6'];
        $udf7=$_POST['udf7'];
        $udf8=$_POST['udf8'];
        $udf9=$_POST['udf9'];
        $udf10=$_POST['udf10'];
        $hash=$_POST['hash'];
        $field1=$_POST['field1'];
        $field2=$_POST['field2'];
        $field3=$_POST['field3'];
        $field4=$_POST['field4'];
        $field5=$_POST['field5'];
        $field6=$_POST['field6'];
        $field7=$_POST['field7'];
        $field8=$_POST['field8'];
        $field9=$_POST['field9'];
        $payment_source=$_POST['payment_source'];
        $PG_TYPE=$_POST['PG_TYPE'];
        $bank_ref_num=$_POST['bank_ref_num'];
        $bankcode =$_POST['bankcode'];
        $error =$_POST['error'];
        $error_Message =$_POST['error_Message'];

        $transcation_array = array(
            "mihpayid" => $mihpayid,
            "mode"  => $mode,
            "status" => $status,
            "unmappedstatus" => $unmappedstatus,
            "key"   => $key,
            "txnid" => $txnid,
            "amount" => $amount,
            "discount" => $discount,
            "net_amount_debit" => $net_amount_debit,
            "addedon"  => $addedon,
            "productinfo" => $productinfo,
            "firstname" => $firstname,
            "lastname" => $lastname,
            "address1" => $address1,
            "address2" => $address2,
            "city"  => $city,
            "state" => $state,
            "country" => $country,
            "zipcode" => $zipcode,
            "email"  => $email,
            "phone" => $phone,
            "udf1" => $udf1,
            "udf2" => $udf2,
            "udf3" => $udf3,
            "udf4" => $udf4,
            "udf5" => $udf5,
            "udf6" => $udf6,
            "udf7" => $udf7,
            "udf8" => $udf8,
            "udf9" => $udf9,
            "udf10" => $udf10,
            "hash" => $hash,
            "field1" => $field1,
            "field2" => $field2,
            "field3" => $field3,
            "field4" => $field4,
            "field5" => $field5,
            "field6" => $field6,
            "field7" => $field7,
            "field8" => $field8,
            "field9" => $field9,
            "payment_source" => $payment_source,
            "PG_TYPE" => $PG_TYPE,
            "bank_ref_num" => $bank_ref_num,
            "bankcode" => $bankcode,
            "error" => $error,
            "error_Message" => $error_Message
        );
        // print_r($new_array); die;
        $jsonData = json_encode($transcation_array);
    
    //----------- responce log entry --------------
    $insert_sql = "INSERT INTO payment_response_log(txnid, response_data, payment_status) VALUES ('$txnid', '$jsonData', '$status')";
    $aResult = mysqli_query($conn, $insert_sql);

    // $mihpayid = '20080004972';
    // $txnid = 'Ytcr-5';
    // $amount = '1.00';
    // $jsonData = 'testdata';
    // $status = 'failure';

    //-------- event details
    // $sel_Sql = "SELECT event_id FROM booking_payment_details WHERE txnid = '$txnid' ";
    $sel_Sql = "SELECT id,event_id FROM booking_payment_details WHERE txnid = '$txnid' ";
    $aResult = mysqli_query($conn, $sel_Sql);
    $event_id = $booking_pay_id = 0;

    if ($aResult->num_rows > 0) {
      while($row = $aResult->fetch_assoc()) {
        $event_id = !empty($row["event_id"]) ? $row["event_id"] : 0;
        $booking_pay_id = !empty($row["id"]) ? $row["id"] : 0;
      }
    }

    //----------- log entry --------------
    $response_datetime = date('Y-m-d H:i:s');
	//$insert_sql = "INSERT INTO booking_payment_log(event_id, txnid, mihpayid, amount, post_data, created_by) VALUES ('1', '$txnid', '$mihpayid', '$amount', '$jsonData', '1')";
	$update_sql = "UPDATE booking_payment_log SET mihpayid = '$mihpayid', response_data = '$jsonData', payment_status = '$status', response_datetime = '$response_datetime' WHERE txnid = '$txnid' ";
    $result = mysqli_query($conn, $update_sql);

    //-------- event booking table update payment status
    $transaction_status = 0;
    if($status == "success"){
        $transaction_status = 1;
    }else{
        $transaction_status = 2;
    }
   
    $update_sql1 = "UPDATE event_booking SET transaction_status = $transaction_status WHERE booking_pay_id = $booking_pay_id ";
    $result1 = mysqli_query($conn, $update_sql1);
   
	// SQL query for insertion
	//$sql = "INSERT INTO new_payment (status, post_data) VALUES ('$status', '$jsonData')";
	$sql = "UPDATE booking_payment_details SET payment_status = '$status', post_data = '$jsonData', payment_mode = '$mode' WHERE txnid = '$txnid' ";

	if ($conn->query($sql) === TRUE) {
	    //echo "New record created successfully";
        if($status == "failure"){
            //header("Location: http://localhost:3000/payment_gateway/".$status);
            if($flag == 1){  // prime
                header("Location: https://swtprime.com/Races2.0_Frontend/register_now/".$event_id."/".$status);
            }else if($flag == 2){  // live
                header("Location: https://racesregistrations.com/register_now/".$event_id."/".$status);
            }else{
                header("Location: http://localhost:3000/register_now/".$event_id."/".$status);
            }
            
        }else if($status == "success"){
            
            if($flag == 1){  // prime
                header("Location: https://swtprime.com/Races2.0_Frontend/payment_gateway/".$status);
            }else if($flag == 2){  // live
                header("Location: https://racesregistrations.com/payment_gateway/".$status);
            }else{
                header("Location: http://localhost:3000/payment_gateway/".$status);
            }
        }
	} else {
	    echo "Error: " . $sql . "<br>" . $conn->error;
	}

	// Close connection
	$conn->close();

?>
