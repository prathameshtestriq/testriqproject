<?php
namespace App\Libraries;

use App\Models\EmailLog;
use Exception;

class Emails
{

    public function post_email($athelete_email, $otp)
    {
        $message = "Dear Customer, Your OTP is: <strong>" . $otp;
        $email = new \SendGrid\Mail\Mail();
        $email->setFrom("support@youtoocantun.com", "YTCR ");
        $email->setSubject("YTCRUN OTP");
        $email->addTo($athelete_email, "YTCR ");
        $email->addContent("text/plain", "Dear Customer, Your OTP is.");
        $email->addContent(
            "text/html",
            $message . "</strong><p>Thank you,<br>YTCRUN</p>"
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

    public function post_email_pwd($athelete_email, $password)
    {
        $message = "Dear Customer, Your password is: <strong>" . $password;
        $email = new \SendGrid\Mail\Mail();
        $email->setFrom("support@youtoocantun.com", "YTCR ");
        $email->setSubject("YTCRUN password");
        $email->addTo($athelete_email, "YTCR ");
        $email->addContent("text/plain", "Dear Customer, Your password is.");
        $email->addContent(
            "text/html",
            $message . "</strong><p>Thank you,<br>YTCRUN</p>"
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
        $message = "You have requested to reset your password. Please click the link below to reset your password.<br>" . $reset_link . "<br><p>Thank you,<br>YTCRUN</p>";
        $email = new \SendGrid\Mail\Mail();
        $email->setFrom("support@youtoocantun.com", "YTCR ");
        $email->setSubject("YTCRUN password");
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
        $mail->setFrom("support@youtoocantun.com", "YTCR ");
        $mail->setSubject("YTCRUN Organiser");
        $mail->addTo($email, $fullname);  // Here $email is the recipient's email and $fullname is the recipient's name
        $mail->addContent("text/plain", "Dear Organiser, ");
        $mail->addContent(
            "text/html",
            "" . $message . "<br><p>Thank you,<br>YTCRUN</p>"
        );
        // dd(env('SEND_GRID_KEY'),$mail);

        $sendgrid = new \SendGrid(env('SEND_GRID_KEY'));
        try {
            $response = $sendgrid->send($mail);
            // dd($response);

            // send mail
            $type = "organiser_contact";
            $send_mail_to = $email;
            $subject = "YTCRUN Organiser";
            $this->save_email_log($type, $send_mail_to, $subject, $message, $response);

            return $response;  // Return response for further processing or logging if needed
        } catch (Exception $e) {
            echo 'Caught exception: ' . $e->getMessage() . "\n";
        }
    }


    public function send_booking_mail($UserId, $UserEmail, $MessageContent, $Subject)
    {
        $email = new \SendGrid\Mail\Mail();
        $email->setFrom("support@youtoocantun.com", "YTCR ");
        $email->setSubject("YTCRUN password");
        $email->addTo($UserEmail, $Subject);
        $email->addContent("text/plain", "Dear, ");
        $email->addContent(
            "text/html",
            "" . $MessageContent . "<br><p>Thank you,<br>YTCRUN</p>"
        );

        $sendgrid = new \SendGrid(env('SEND_GRID_KEY'));
        try {
            $response = $sendgrid->send($email);
            // send mail
            $type = "ticket_booking";
            $send_mail_to = $UserEmail;
            $this->save_email_log($type, $send_mail_to, $Subject, $MessageContent, $response);

        } catch (Exception $e) {
            echo 'Caught exception: ' . $e->getMessage() . "\n";
        }
    }

    public function registered_email($mail)
    {
        $email = new \SendGrid\Mail\Mail();
        $email->setFrom("support@youtoocantun.com", "YTCR ");
        $email->setSubject("Registration");
        $email->addTo($mail, "Registration");
        $email->addContent("text/plain", "Dear Customer, ");
        $email->addContent(
            "text/html",
            "You have successfuly registered. <br><p>Thank you,<br>YTCRUN</p>"
        );
        $sendgrid = new \SendGrid(env('SEND_GRID_KEY'));
        try {
            $response = $sendgrid->send($email);
            // send mail
            $type = "registration";
            $send_mail_to = $mail;
            $subject = "Registration";
            $this->save_email_log($type, $send_mail_to, $subject, "Registration", $response);

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

