<?php

namespace Matcha\Lib;

class Mail
{
    private $email;
    private $from_name = "Matcha";
    private $from_mail = "jde-la-m@student.le-101.fr";
    private $host = "http:" . URL;

    public function __construct($email)
    {
        if (!empty($email))
            $this->email = $email;
    }

    public function OAuthRegistrationMail($email, $pwd)
    {
        $mail_subject = "Welcome to Matcha";
        $mail_message = '
		<div style="text-align: center; font-family: Verdana, Geneva, Tahoma, sans-serif">
			<h3 style="color: #E60A54;">Welcome to Matcha!</h3>
			<h5>We are happy to have you on board. Here are your credentials:</h5>
			<p style="color: #E60A54;">
				<br>
				Email: <b>' . $email . '</b>
				<br>
				Password: <b>' . $pwd . '</b>
				<br>
			</p>
			<br>
			<br>
			<p>Looking forward to see more of you!</p>
			<p><img src="https://i.imgur.com/wbNxLWd.png" width="100px" height="auto" alt="Matcha"/></p>
		</div>';
        $this->sendMail($mail_subject, $mail_message);
    }

    public function registrationMail($username, $token)
    {
        $mail_subject = "Matcha account confirmation";
        $mail_link = $this->host . "signup/processConfirmation/" . $username . '/' . $token;
        $mail_message = '
		<div style="text-align: center; font-family: Verdana, Geneva, Tahoma, sans-serif">
			<h3 style="color: #E60A54;">Welcome to Matcha!</h3>
			<h5>We are happy to have you on board. In order to confirm your account, please click this button:</h5>
			<p>
				<br>
				<a href="' . $mail_link . '" role="button" style="background-color: #E60A54; padding: 10px; border-radius: 5px; text-decoration: none; color: white;">Confirm Account</a>
				<br>
				<br>
			</p>
			<small>If you have problems accessing the button, you can also paste this link into your browser: <a href="' . $mail_link . '">' . $mail_link . '</a></small>
			<br>
			<br>
			<p>Looking forward to see more of you!</p>
			<p><img src="https://i.imgur.com/wbNxLWd.png" width="100px" height="auto" alt="Matcha"/></p>
		</div>';
        $this->sendMail($mail_subject, $mail_message);
    }

    public function forgotPasswordMail($username, $token)
    {
        $mail_subject = "Reset your password";
        $mail_link = $this->host . "login/resetpassword/" . $username . '/' . $token;
        $mail_message = '
		<div style="text-align: center; font-family: Verdana, Geneva, Tahoma, sans-serif">
			<p>You have requested to reset your password. In order to do so, please click this button:</p>
			<p>
				<br>
				<a href="' . $mail_link . '" role="button" style="background-color: #E60A54; padding: 10px; border-radius: 5px; text-decoration: none; color: white;">Reset Password</a>
				<br>
				<br>
			</p>
			<small>If you have problems accessing the button, you can also paste this link into your browser: <a href="' . $mail_link . '">' . $mail_link . '</a></small>
			<br>
			<br>
			<p>
				If this wasn\'t you, please ignore this email.
			</p>
			<p>
				<img src="https://i.imgur.com/wbNxLWd.png" width="100px" height="auto" alt="Matcha"/>
			</p>
		</div>';
        $this->sendMail($mail_subject, $mail_message);
    }
    public function newMessageMail($sender, $messageBody)
    {
        $mail_subject = "Someome started a conversation with you";
        $mail_link = $this->host . "messages/t/" . $sender;
        $mail_message = '
		<div style="text-align: center; font-family: Verdana, Geneva, Tahoma, sans-serif">
			<p><b>' . $sender . '</b> messaged you for the fist time, here\'s what they said:</p>
			<p>
			<br>
			<a href="' . $mail_link . '" role="button" style="background-color: #DDDDDD; padding: 20px 100px; border-radius: 5px; text-decoration: none; color: black; font-style: italic; font-size: 11px;" >' . $messageBody . '</a>
			<br><br>
			</p>
			<small>Access your post directly <a href="' . $mail_link . '">' . $mail_link . '</a></small>
			<br><br>
			<p>Looking forward to see more of you!</p>
			<p><img src="https://i.imgur.com/wbNxLWd.png" width="100px" height="auto" alt="Matcha"/></p>
		</div>';
        $this->sendMail($mail_subject, $mail_message);
    }

    public function newLikeMail($liker)
    {
        $mail_subject = "Someome liked you";
        $mail_link = $this->host . "profile/v/" . $liker;
        $mail_message = '
		<div style="text-align: center; font-family: Verdana, Geneva, Tahoma, sans-serif">
			<p><b>' . $liker . '</b> liked you!</p>
			<small>Access their profile directly <a href="' . $mail_link . '">' . $mail_link . '</a></small>
			<br><br>
			<p>Looking forward to see more of you!</p>
			<p><img src="https://i.imgur.com/wbNxLWd.png" width="100px" height="auto" alt="Matcha"/></p>
		</div>';
        $this->sendMail($mail_subject, $mail_message);
    }

    public function newVisitMail($visitor)
    {
        $mail_subject = "Someome visted your profile";
        $mail_link = $this->host . "profile/v/" . $visitor;
        $mail_message = '
		<div style="text-align: center; font-family: Verdana, Geneva, Tahoma, sans-serif">
			<p><b>' . $visitor . '</b> visited you!</p>
			<small>Access their profile directly <a href="' . $mail_link . '">' . $mail_link . '</a></small>
			<br><br>
			<p>Looking forward to see more of you!</p>
			<p><img src="https://i.imgur.com/wbNxLWd.png" width="100px" height="auto" alt="Matcha"/></p>
		</div>';
        $this->sendMail($mail_subject, $mail_message);
    }

    private function sendMail($mail_subject, $mail_message)
    {
        $encoding = "utf-8";
        $subject_preferences = array(
            "input-charset" => $encoding,
            "output-charset" => $encoding,
            "line-length" => 76,
            "line-break-chars" => "\r\n"
        );
        $header = "Content-type: text/html; charset=" . $encoding . " \r\n";
        $header .= "From: " . $this->from_name . " <" . $this->from_mail . "> \r\n";
        $header .= "MIME-Version: 1.0 \r\n";
        $header .= "Content-Transfer-Encoding: 8bit \r\n";
        $header .= "Date: " . date("r (T)") . " \r\n";
        $header .= iconv_mime_encode("Subject", $mail_subject, $subject_preferences);

        mail($this->email, $mail_subject, $mail_message, $header);
    }
}
