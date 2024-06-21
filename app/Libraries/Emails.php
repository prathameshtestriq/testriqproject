<?php
namespace App\Libraries;

use App\Models\EmailLog;
use Exception;

class Emails
{

    public function post_email($athelete_email, $otp, $firstname, $lastname)
    {
        $message = "Dear " . $firstname . " " . $lastname . ",
 <br/><br/>
Your One-Time Password (OTP) for accessing your account is: <b>" . $otp . "</b>
 <br/><br/>
Please enter this code on the verification page to proceed. This OTP is valid for the next 10 minutes.
 <br/><br/>
Please ignore this email or contact our support team immediately if you did not request this code.
 <br/><br/>
Thank you for choosing RACES.
 <br/><br/>
Best regards,
 <br/><br/>
(For RACES)<br/>
Team YouTooCanRun";
        $email = new \SendGrid\Mail\Mail();
        $email->setFrom("support@youtoocantun.com", "RACES");
        $email->setSubject("Your OTP Code for Secure Access");
        $email->addTo($athelete_email, "RACES ");
        $email->addContent("text/plain", "Dear Customer, Your OTP is.");
        $email->addContent(
            "text/html",
            $message
        );

        $sendgrid = new \SendGrid(env('SEND_GRID_KEY'));
        // try {
        $response = $sendgrid->send($email);
        // send mail
        $type = "email_otp";
        $send_mail_to = $athelete_email;
        $subject = "Email Otp";
        $this->save_email_log($type, $send_mail_to, $subject, $message, $response);
        // } catch (Exception $e) {
        // 	echo 'Caught exception: '. $e->getMessage() ."\n";
        // }
    }

    public function post_email_pwd($athelete_email, $firstname, $lastname, $password)
    {
        $message = "Dear " . $firstname . " " . $lastname . ",<br/>
We wanted to inform you that your password has been successfully reset. You can now log in to your RACES account using your new password.<br/>
If you did not initiate this password reset, please contact our support team immediately to ensure the security of your account.<br/>
Thank you for your attention to this matter.<br/>";
        $email = new \SendGrid\Mail\Mail();
        $email->setFrom("support@youtoocantun.com", "RACES ");
        $email->setSubject("Your Password Has Been Successfully Reset");
        $email->addTo($athelete_email, "RACES ");
        $email->addContent("text/plain", "Dear Customer, Your password is.");
        $email->addContent(
            "text/html",
            $message . "<p>Best regards,<br>(For RACES)<br>Team YouTooCanRun</p>"
        );

        $sendgrid = new \SendGrid(env('SEND_GRID_KEY'));
        // try {
        $response = $sendgrid->send($email);
        // send mail
        $type = "reset_password";
        $send_mail_to = $athelete_email;
        $subject = "Post Email Password";
        $this->save_email_log($type, $send_mail_to, $subject, $message, $response);
        // } catch (Exception $e) {
        // 	echo 'Caught exception: '. $e->getMessage() ."\n";
        // }
    }

    public function send_reset_password_link($athelete_email, $reset_link)
    {
        $message = "You have requested to reset your password. Please click the link below to reset your password.<br>" . $reset_link . "<br><p>Best regards,<br>(For RACES)<br>Team YouTooCanRun</p>";
        $email = new \SendGrid\Mail\Mail();
        $email->setFrom("support@youtoocantun.com", "RACES Registrations ");
        $email->setSubject("RACES password");
        $email->addTo($athelete_email, "Reset Your Password");
        $email->addContent("text/plain", "Dear Customer, ");
        $email->addContent(
            "text/html",
            $message
        );
        $sendgrid = new \SendGrid(env('SEND_GRID_KEY'));
        try {
            $response = $sendgrid->send($email);
            // send mail
            $type = "reset_password";
            $send_mail_to = $athelete_email;
            $subject = "Reset Your Password";
            $this->save_email_log($type, $send_mail_to, $subject, $message, $response);

        } catch (Exception $e) {
            echo 'Caught exception: ' . $e->getMessage() . "\n";
        }
    }

    public function send_org_notification($fullname, $email, $contact_no, $message)
    {
        $mail = new \SendGrid\Mail\Mail();  // Renamed the variable to $mail to avoid conflict
        $mail->setFrom("support@youtoocantun.com", "RACES Registrations ");
        $mail->setSubject("RACES Organiser");
        $mail->addTo($email, $fullname);  // Here $email is the recipient's email and $fullname is the recipient's name
        $mail->addContent("text/plain", "Dear Organiser, ");
        $mail->addContent(
            "text/html",
            "" . $message . "<br><p>Best regards,<br>(For RACES)<br>Team YouTooCanRun</p>"
        );
        // dd(env('SEND_GRID_KEY'),$mail);

        $sendgrid = new \SendGrid(env('SEND_GRID_KEY'));
        try {
            $response = $sendgrid->send($mail);
            // dd($response);

            // send mail
            $type = "organiser_contact";
            $send_mail_to = $email;
            $subject = "RACES Organiser";
            $this->save_email_log($type, $send_mail_to, $subject, $message, $response);

            return $response;  // Return response for further processing or logging if needed
        } catch (Exception $e) {
            echo 'Caught exception: ' . $e->getMessage() . "\n";
        }
    }


    public function send_booking_mail($UserId, $UserEmail, $MessageContent, $Subject, $flag=0)
    {
        $email = new \SendGrid\Mail\Mail();
        $email->setFrom("support@youtoocantun.com", "RACES Registrations ");
        $email->setSubject($Subject);
        $email->addTo($UserEmail, $Subject);
        $email->addContent("text/plain", "Dear, ");
        $email->addContent(
            "text/html",
            "" . $MessageContent
        );

        $sendgrid = new \SendGrid(env('SEND_GRID_KEY'));
        try {
            $response = $sendgrid->send($email);
            // send mail
            if ($Subject == "Welcome to RACES - Organiser Onboarding Successful") {
                $type = "Organiser Email";
            } else if($flag == 2){
               $type = "Manual Attendee Email";
            }else {
                $type = "Ticket Booking";
            }

            $send_mail_to = $UserEmail;
            $this->save_email_log($type, $send_mail_to, $Subject, $MessageContent, $response);

        } catch (Exception $e) {
            echo 'Caught exception: ' . $e->getMessage() . "\n";
        }
    }

    public function registered_email($mail, $firstname, $lastname)
    {
        $message = "Dear " . $firstname . " " . $lastname . ",
 <br/><br/>
Thank you for registering with RACES! We are excited to have you join our community.
 <br/><br/>
If you have any questions or need assistance, feel free to reach out to our support team.
 <br/><br/>
 Email: support@youtoocanrun.com<br/>
Phone Number:+91 9920142195
 <br/><br/>
Welcome aboard!
 <br/><br/>
<p>Best regards,<br>(For RACES)<br>Team YouTooCanRun</p>";
        $email = new \SendGrid\Mail\Mail();
        $email->setFrom("support@youtoocantun.com", "RACES Registrations"); //YouTooCanRun
        $email->setSubject("Welcome to RACES!");
        $email->addTo($mail, "Registration");
        $email->addContent("text/plain", "Dear Customer, ");
        $email->addContent(
            "text/html",
            $message
        );
        $sendgrid = new \SendGrid(env('SEND_GRID_KEY'));
        try {
            $response = $sendgrid->send($email);
            // send mail
            $type = "registration";
            $send_mail_to = $mail;
            $subject = "Registration";
            $this->save_email_log($type, $send_mail_to, $subject, $message, $response);

        } catch (Exception $e) {
            echo 'Caught exception: ' . $e->getMessage() . "\n";
        }
    }

    public function save_email_log($type, $send_mail_to, $subject, $message, $response)
    {
        $responseData = [
            'statusCode' => $response->statusCode(),
            'body' => $response->body(),
            'headers' => $response->headers(),
        ];
        // $responseData = [];

        $email_log = new EmailLog();
        $email_log->type = $type;
        $email_log->send_mail_to = $send_mail_to;
        $email_log->subject = $subject;
        $email_log->message = $message;
        $email_log->datetime = strtotime("now");
        $email_log->response = json_encode($responseData);
        $email_log->save();
    }
}

