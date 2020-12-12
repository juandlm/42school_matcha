<?php

namespace Matcha\Controller;

use Matcha\Core\Controller;
use Matcha\Lib\Alert;

class SignupController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->checkAccess("You're already logged in.", URL, true);
    }

    public function index()
    {
        return ($this->render("index"));
    }

    public function success()
    {
        return ($this->render("success"));
    }

    public function processSignup()
    {
        if ($this->isAjaxRequest()) {
            if (!empty($_POST)) {
                $this->secureForm($_POST);
                $userManager = new \Matcha\Model\UserManager();
                if (!empty($_POST["checkexisting"]))
                    exit(json_encode([
                        "exists" => $userManager->existsUser($_POST["username"] ?? null, $_POST["email"] ?? null)
                    ]));
                $username = $_POST["username"];
                $email = $_POST["email"];
                $password = $_POST["password"];
                $passwordconfirm = $_POST["cpassword"];
                if (!empty($_POST["toscheck"])) {
                    if (preg_match("/^[a-z\d_-]{3,20}$/i", $username)) {
                        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                            $userExists = $userManager->existsUser($username, $email);
                            if ($userExists == 0) {
                                if (preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[\s\S]{6,16}$/", $password)) {
                                    if ($password === $passwordconfirm) {
                                        $password = password_hash($password, PASSWORD_DEFAULT);
                                        $token = bin2hex(random_bytes(16));
                                        $new_user = new \Matcha\Model\User([
                                            "usr_login"    => $username,
                                            "usr_pwd"    => $password,
                                            "usr_ppic"    => "nopic.png",
                                            "usr_email"    => $email,
                                            "usr_lat" => $_SESSION["ip_info"]["lat"] ?? null,
                                            "usr_long" => $_SESSION["ip_info"]["long"] ?? null,
                                            "usr_city" => $_SESSION["ip_info"]["city"] ?? null,
                                            "usr_country" => $_SESSION["ip_info"]["country"] ?? null,
                                            "usr_social" => 0,
                                            "usr_idsocial" => 0,
                                            "usr_token"    => $token
                                        ]);
                                        $userManager->newUser($new_user);
                                        $sendMail = new \Matcha\Lib\Mail($email);
                                        $sendMail->registrationMail($username, $token);
                                        exit(json_encode([true, null]));
                                    } else
                                        exit(json_encode([false, "The passwords you entered don't match."]));
                                } else
                                    exit(json_encode([false, "Passwords must be between 6 and 16 characters long and contain at least one uppercase letter, one lowercase letter and one digit."]));
                            } else
                                exit(json_encode([false, "This username or email address is already in use."]));
                        } else
                            exit(json_encode([false, "The email address you entered is not valid."]));
                    } else
                        exit(json_encode([false, "Usernames must be between 3 and 20 characters long and can only contain alphanumeric, underscore and hyphen characters."]));
                } else
                    exit(json_encode([false, "You have to accept the Terms of Service in order to register."]));
            } else
                exit(json_encode([false, "There was a problem with the data you submitted."]));
        } else
            $this->redirect(URL);
    }

    public function processConfirmation($username = null, $token = null)
    {
        if (!empty($username) && !empty($token)) {
            $user = new \Matcha\Model\User(["usr_login" => $username]);
            $userManager = new \Matcha\Model\UserManager();
            $userData = $userManager->getUser($user);
            if ($userData->get_usr_confirmed() == 0) {
                if (strcmp($token, $userData->get_usr_token() === 0)) {
                    $userManager->confirmUser($userData);
                    Alert::success_alert("Your account is now active, thank you!");
                    $this->redirect(URL . "login");
                } else {
                    Alert::danger_alert("Invalid token. Your account couldn't be confirmed.");
                    $this->redirect(URL . "login");
                }
            } else {
                Alert::warning_alert("Your account has already been confirmed.");
                $this->redirect(URL . "login");
            }
        } else {
            Alert::danger_alert("There was a probem with the data you submitted.");
            $this->redirect(URL);
        }
    }
}
