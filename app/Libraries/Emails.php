<?php
namespace App\Libraries;

use Exception;

class Emails
{

    public function post_email($athelete_email, $otp)
    {
        $email = new \SendGrid\Mail\Mail();
        $email->setFrom("test@example.com", "YTCR Athlete");
        $email->setSubject("YTCRun OTP");
        $email->addTo($athelete_email, "YTCR Athlete");
        $email->addContent("text/plain", "Dear Customer, Your OTP is.");
        $email->addContent(
            "text/html",
            "Dear Customer, Your OTP is: <strong>" . $otp . "</strong><p>Thank you,<br>YTCRUN</p>"
        );

        $sendgrid = new \SendGrid(env('SENDGRID_API_KEY'));
        // try {
        $response = $sendgrid->send($email);
        // } catch (Exception $e) {
        // 	echo 'Caught exception: '. $e->getMessage() ."\n";
        // }
    }

    public function post_email_pwd($athelete_email, $password)
    {
        $email = new \SendGrid\Mail\Mail();
        $email->setFrom("test@example.com", "YTCR Athlete");
        $email->setSubject("YTCRun password");
        $email->addTo($athelete_email, "YTCR Athlete");
        $email->addContent("text/plain", "Dear Customer, Your password is.");
        $email->addContent(
            "text/html",
            "Dear Customer, Your password is: <strong>" . $password . "</strong><p>Thank you,<br>YTCRUN</p>"
        );

        $sendgrid = new \SendGrid(env('SENDGRID_API_KEY'));
        // try {
        $response = $sendgrid->send($email);
        // } catch (Exception $e) {
        // 	echo 'Caught exception: '. $e->getMessage() ."\n";
        // }
    }

    public function send_reset_password_link($athelete_email, $reset_link)
    {
        $email = new \SendGrid\Mail\Mail();
        $email->setFrom("test@example.com", "YTCR Athlete");
        $email->setSubject("YTCRun password");
        $email->addTo($athelete_email, "Reset Your Password");
        $email->addContent("text/plain", "Dear Customer, ");
        $email->addContent(
            "text/html",
            "You have requested to reset your password. Please click the link below to reset your password.<br>" . $reset_link . "<br><p>Thank you,<br>YTCRUN</p>"
        );

        $sendgrid = new \SendGrid(env('SENDGRID_API_KEY'));
        try {
            $response = $sendgrid->send($email);
        } catch (Exception $e) {
            echo 'Caught exception: ' . $e->getMessage() . "\n";
        }
    }

    public function send_org_notification($fullname, $email, $contact_no, $message)
    {
        $email = new \SendGrid\Mail\Mail();
        $email->setFrom("test@example.com", "YTCR Athlete");
        $email->setSubject("YTCRun password");
        $email->addTo($email, "Reset Your Password");
        $email->addContent("text/plain", "Dear Organiser, ");
        $email->addContent(
            "text/html",
            "" . $message . "<br><p>Thank you,<br>YTCRUN</p>"
        );

        $sendgrid = new \SendGrid(env('SENDGRID_API_KEY'));
        try {
            $response = $sendgrid->send($email);
        } catch (Exception $e) {
            echo 'Caught exception: ' . $e->getMessage() . "\n";
        }
    }

    public function send_booking_mail($UserId, $UserEmail,$MessageContent,$Subject)
    {
        $email = new \SendGrid\Mail\Mail();
        $email->setFrom("test@example.com", "YTCR Athlete");
        $email->setSubject("YTCRun password");
        $email->addTo($UserEmail, $Subject);
        $email->addContent("text/plain", "Dear, ");
        $email->addContent(
            "text/html",
            "" . $MessageContent . "<br><p>Thank you,<br>YTCRUN</p>"
        );

        $sendgrid = new \SendGrid(env('SENDGRID_API_KEY'));
        try {
            $response = $sendgrid->send($email);
        } catch (Exception $e) {
            echo 'Caught exception: ' . $e->getMessage() . "\n";
        }
    }
}

