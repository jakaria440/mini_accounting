<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once dirname(__DIR__) . '/vendor/autoload.php';

function sendResetPasswordMail($email, $name, $token) {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->SMTPDebug = SMTP::DEBUG_OFF;
        $mail->isSMTP();
        $mail->Host = 'smtp.titan.email';
        $mail->SMTPAuth = true;
        $mail->Username = 'al-barakah@addohafood.com';
        $mail->Password = 'A@12345678';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->CharSet = 'UTF-8';

        // Recipients
        $mail->setFrom('al-barakah@addohafood.com', 'আল-বারাকাহ তহবিল');
        $mail->addAddress($email, $name);
        $mail->addBCC('albarakah.phultala@gmail.com', 'আল-বারাকাহ এডমিন');

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'পাসওয়ার্ড রিসেট লিংক - আল-বারাকাহ তহবিল';

        $reset_link = 'https://barakah.addohafood.com/pages/pass_reset.php?token=' . $token;
        
        $mail->Body = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;'>
                <div style='text-align: center; margin-bottom: 20px;'>
                    <img src='https://barakah.addohafood.com/assets/logo.png' height='60'/>
                    <h2>আল-বারাকাহ তহবিল</h2>
                </div>
                <p>প্রিয় {$name},</p>
                <p>আপনার পাসওয়ার্ড রিসেট করার জন্য নিচের লিংকে ক্লিক করুন:</p>
                <div style='text-align: center; margin: 30px 0;'>
                    <a href='{$reset_link}' 
                       style='background-color: #007bff; 
                              color: white; 
                              padding: 12px 25px; 
                              text-decoration: none; 
                              border-radius: 5px;
                              font-size: 16px;'>
                        পাসওয়ার্ড রিসেট করুন
                    </a>
                </div>
                <p>অথবা এই লিংক কপি করে ব্রাউজারে পেস্ট করুন:</p>
                <p style='background-color: #f8f9fa; padding: 10px; border-radius: 4px;'>{$reset_link}</p>
                <div style='margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee;'>
                    <p style='color: #666; font-size: 14px;'>
                        • এই লিংক ১ ঘণ্টার জন্য কার্যকর থাকবে।<br>
                        • যদি আপনি পাসওয়ার্ড রিসেট করার অনুরোধ না করে থাকেন, তাহলে এই ইমেইল উপেক্ষা করুন।
                    </p>
                </div>
            </div>
        ";

        return $mail->send();
    } catch (Exception $e) {
        error_log("Mail Error: " . $mail->ErrorInfo);
        return false;
    }
}