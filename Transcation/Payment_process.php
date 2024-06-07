<?php 
//print_r($_REQUEST); die;
//header("Location: http://localhost:3000/payment_gateway");
//-------------------

// Database connection parameters
$servername = "localhost"; 
$username = "root"; 
$password = "12345"; 
$database = "Races2.0_Web"; 

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
        $amount=$_POST['amount'];
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


// SQL query for insertion
$sql = "INSERT INTO new_payment (status, post_data) VALUES ('$status', '$jsonData')";

if ($conn->query($sql) === TRUE) {
    //echo "New record created successfully";
    header("Location: http://localhost:3000/payment_gateway/");
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

// Close connection
$conn->close();

?>
