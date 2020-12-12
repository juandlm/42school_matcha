<?php

namespace Matcha\Controller;

use Matcha\Core\Controller;
use Matcha\Lib\Alert;

class LoginController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->checkAccess("You're already logged in.", URL, true);
    }

    public function index()
    {
        return ($this->render('index'));
    }

    public function forgotPassword()
    {
        return ($this->render("forgotpassword"));
    }

    public function resetPassword(string $username = null, string $token = null)
    {
        if (!empty($username) && !empty($token)) {
            $username = $this->secureInput($username);
            $token = $this->secureInput($token);
            $userData = $this->fetchUserData($username);
            if (
                !empty($userData->get_usr_id())
                && !empty($userData->get_usr_token())
            ) {
                if (strcmp($userData->get_usr_token(), $token) === 0) {
                    unset($_SESSION["reset_pwd"]);
                    $_SESSION["reset_pwd"]["username"] = $username;
                    $_SESSION["reset_pwd"]["token"] = $token;
                    return ($this->render("resetpassword"));
                } else {
                    Alert::danger_alert("Invalid data or expired token.");
                    $this->redirect(URL . "login");
                }
            } else {
                Alert::danger_alert("Invalid data or expired token.");
                $this->redirect(URL . "login");
            }
        } else {
            Alert::danger_alert("There was a probem with the data you submitted.");
            $this->redirect(URL);
        }
    }

    public function processLogin()
    {
        if (!empty($_POST)) {
            $this->secureForm($_POST);
            $crendential = $_POST["login"];
            $password = $_POST["password"];
            $userManager = new \Matcha\Model\UserManager();
            $userLogin = new \Matcha\Model\User([
                (filter_var($crendential, FILTER_VALIDATE_EMAIL) ? "usr_email" : "usr_login") => $crendential
            ]);
            $loginData = $userManager->getUser($userLogin);
            if (
                stristr($loginData->get_usr_login(), $crendential)
                || stristr($loginData->get_usr_email(), $crendential)
            ) {
                if (password_verify($password, $loginData->get_usr_pwd())) {
                    if ($loginData->get_usr_confirmed() == 0) {
                        $token = bin2hex(random_bytes(16));
                        $loginData->set_usr_token($token);
                        $userManager->editUser($loginData);
                        $send_mail = new \Matcha\Lib\Mail($loginData->get_usr_email());
                        $send_mail->registrationMail($loginData->get_usr_login(), $loginData->get_usr_token());
                        Alert::warning_alert("Your account has not been verified, we have sent another confirmation e-mail in order for you to verify it. Make sure to check your Spam folder.");
                        $this->redirect(".");
                    } else {
                        $landing = $this->createUserSession($loginData);
                        if ($loginData->get_usr_active() == 0) {
                            $userManager->activateUser($loginData);
                            Alert::success_alert("Your account has been reactivated. Welcome back!");
                        } else
                            $landing === URL ?
                                Alert::success_alert("You are now logged in.") :
                                Alert::warning_alert("Welcome to Matcha. We need to know a little bit more about you, please complete your profile.");
                        $this->redirect($landing);
                    }
                } else {
                    Alert::danger_alert("The password you entered is incorrect.");
                    $this->redirect(".");
                }
            } else {
                Alert::danger_alert("This account does not exist.");
                $this->redirect(".");
            }
        } else {
            Alert::danger_alert("There was a problem with the data you submitted.");
            $this->redirect(".");
        }
    }

    public function processForgotPassword()
    {
        if (!empty($_POST)) {
            $this->secureForm($_POST);
            $email = $_POST["email"];
            $token = bin2hex(random_bytes(16));
            $userManager = new \Matcha\Model\UserManager();
            if ($userManager->existsUser(null, $email) == 1) {
                $user = new \Matcha\Model\User(["usr_email" => $email]);
                $userData = $userManager->getUser($user);
                $userData->set_usr_token($token);
                $userManager->editUser($userData);
                $new_mail = new \Matcha\Lib\Mail($userData->get_usr_email());
                $new_mail->forgotPasswordMail($userData->get_usr_login(), $userData->get_usr_token());
            }
            Alert::success_alert("If an account is associated with this email address, an email has been sent.");
            $this->redirect(URL . "login/forgotpassword");
        } else {
            Alert::danger_alert("There was a problem with the data you submitted.");
            $this->redirect(URL);
        }
    }

    public function processResetPassword()
    {
        if (
            !empty($_POST)
            && !empty($_SESSION["reset_pwd"]["username"])
            && !empty($_SESSION["reset_pwd"]["token"])
        ) {
            $this->secureForm($_POST);
            $username = $_SESSION["reset_pwd"]["username"];
            $userData = $this->fetchUserData($username);
            $token2 = bin2hex(random_bytes(16));
            $userData->set_usr_token($token2);
            $userManager = new \Matcha\Model\UserManager();
            $userManager->editUser($userData);
            if (strcmp($_POST["cnew_password"], $_POST["new_password"]) === 0) {
                $newpassword = $_POST["new_password"];
                if (preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[\s\S]{6,16}$/", $newpassword))
                    $newpassword = password_hash($newpassword, PASSWORD_DEFAULT);
                else {
                    Alert::danger_alert("Passwords must be between 6 and 16 characters long and contain at least one uppercase letter, one lowercase letter and one digit.");
                    $this->redirect(URL . "login/resetpassword/" . $username . '/' . $token2);
                }
            } else {
                Alert::danger_alert("The new passwords you entered don't match.");
                $this->redirect(URL . "login/resetpassword/" . $username . '/' . $token2);
            }
            $userData->set_usr_token(NULL);
            $userData->set_usr_pwd($newpassword);
            $userManager->editUser($userData);
            unset($_SESSION["reset_pwd"]);
            Alert::success_alert("Your password has been reset, you can now log back in.");
            $this->redirect(URL . "login");
        } else {
            Alert::danger_alert("There was a probem with the data you submitted.");
            $this->redirect(URL);
        }
    }
}
