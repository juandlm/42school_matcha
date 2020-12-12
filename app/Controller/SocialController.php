<?php

namespace Matcha\Controller;

use Matcha\Core\Controller;
use Matcha\Lib\Alert;
use Matcha\Lib\OAuth2;

class SocialController extends Controller
{
    public function facebook()
    {
        $CLIENT_ID     = "697157987426399";
        $CLIENT_SECRET = "6e5d1dc629979d3bd0481f052270a2e2";

        $REDIRECT_URI           = "http://localhost:8008/social/facebook";
        $AUTHORIZATION_ENDPOINT = "https://graph.facebook.com/oauth/authorize";
        $TOKEN_ENDPOINT         = "https://graph.facebook.com/oauth/access_token";

        $client = new OAuth2\Client($CLIENT_ID, $CLIENT_SECRET);

        if (!isset($_GET["code"])) {
            $auth_url = $client->getAuthenticationUrl($AUTHORIZATION_ENDPOINT, $REDIRECT_URI, ["scope" => "email"]);
            header('Location: ' . $auth_url);
        } else {
            $params = ["code" => $_GET["code"], "redirect_uri" => $REDIRECT_URI];
            $response = $client->getAccessToken($TOKEN_ENDPOINT, "authorization_code", $params);
            if (isset($response["result"]["error"])) {
                Alert::danger_alert("There was an error with the authorization process.");
                $this->redirect(URL);
            } else {
                extract(["info" => $response["result"]]);
                $client->setAccessToken($info["access_token"]);
                $response = $client->fetch("https://graph.facebook.com/me", ["fields" => "email,name"]);
                var_dump($response);
                $result = $response["result"];
                if ($result) {
                    $account = [
                        "id_social"        => $result["id"],
                        "email"            => $result["email"],
                        "username"        => substr($result["name"], 0, 2) . bin2hex(random_bytes(4)),
                        "displayname"    => $result["name"],
                        "password"        => bin2hex(random_bytes(8)),
                        "social"        => 1
                    ];
                    $userManager = new \Matcha\Model\UserManager();
                    if (empty($userManager->getUserSocial($result["id"])))
                        $this->signupSocial($account);
                    $getUserSocial = $userManager->getUserSocial($result["id"]);
                    $loginData = $this->fetchUserData($getUserSocial->usr_login);
                    $landing = $this->createUserSession($loginData, false);
                    if ($loginData->get_usr_active() == 0) {
                        $userManager->activateUser($loginData);
                        Alert::success_alert("Your account has been reactivated. Welcome back!");
                    } else
                        $landing === URL ?
                            Alert::success_alert("You are now logged in.") :
                            Alert::warning_alert("Welcome to Matcha. We need to know a little bit more about you, please complete your profile.");
                    $this->redirect($landing);
                }
            }
        }
    }

    public function intra()
    {
        $CLIENT_ID     = "915bcdfa1ddb27ed58cf0d2119fbf917319a5f5c81239d5c7874e69dacba47ce";
        $CLIENT_SECRET = "bb0aff5c3e1a2e7f9bbdfded8d21958cf8798396dd7ff2fde9a5b7ae9c2de608";

        $REDIRECT_URI           = "http://localhost:8008/social/intra";
        $AUTHORIZATION_ENDPOINT = "https://api.intra.42.fr/oauth/authorize";
        $TOKEN_ENDPOINT         = "https://api.intra.42.fr/oauth/token";

        $client = new OAuth2\Client($CLIENT_ID, $CLIENT_SECRET);

        if (!isset($_GET["code"])) {
            $auth_url = $client->getAuthenticationUrl($AUTHORIZATION_ENDPOINT, $REDIRECT_URI);
            header('Location: ' . $auth_url);
        } else {
            $params = ["code" => $_GET["code"], "redirect_uri" => $REDIRECT_URI];
            $response = $client->getAccessToken($TOKEN_ENDPOINT, "authorization_code", $params);
            if (isset($response["result"]["error"])) {
                Alert::danger_alert("There was an error with the authorization process.");
                $this->redirect(URL);
            } else {
                extract(["info" => $response["result"]]);
                $client->setAccessToken($info["access_token"]);
                $response = $client->fetch("https://api.intra.42.fr/v2/me");
                $result = $response["result"];
                if ($result) {
                    $account = [
                        "id_social"        => $result["id"],
                        "email"            => $result["email"],
                        "username"        => $result["login"],
                        "displayname"    => $result["displayname"],
                        "password"        => bin2hex(random_bytes(8)),
                        "social"        => 2
                    ];
                    $userManager = new \Matcha\Model\UserManager();
                    if (empty($userManager->getUserSocial($result["id"])))
                        $this->signupSocial($account);
                    $getUserSocial = $userManager->getUserSocial($result["id"]);
                    $loginData = $this->fetchUserData($getUserSocial->usr_login);
                    $landing = $this->createUserSession($loginData, false);
                    if ($loginData->get_usr_active() == 0) {
                        $userManager->activateUser($loginData);
                        Alert::success_alert("Your account has been reactivated. Welcome back!");
                    } else
                        $landing === URL ?
                            Alert::success_alert("You are now logged in.") :
                            Alert::warning_alert("Welcome to Matcha. We need to know a little bit more about you, please complete your profile.");
                    $this->redirect($landing);
                }
            }
        }
    }

    public function signupSocial($data)
    {
        $userManager = new \Matcha\Model\UserManager();
        if ($userManager->existsUser(null, $data["email"])) {
            Alert::danger_alert("User with this email is already registered");
            $this->redirect(URL . "login");
        }
        if ($userManager->existsUser($data["username"], null))
            $data["username"] .= bin2hex(random_bytes(3));
        $clearpwd = $data["password"];
        $data["password"] = password_hash($data["password"], PASSWORD_DEFAULT);
        $new_user = new \Matcha\Model\User([
            "usr_login"    => $data["username"],
            "usr_pwd"    => $data["password"],
            "usr_ppic"    => "nopic.png",
            "usr_email"    => $data["email"],
            "usr_name" => null,
            "usr_social" => $data["social"],
            "usr_idsocial" => $data["id_social"],
            "usr_lat" => $_SESSION["ip_info"]["lat"] ?? null,
            "usr_long" => $_SESSION["ip_info"]["long"] ?? null,
            "usr_city" => $_SESSION["ip_info"]["city"] ?? null,
            "usr_country" => $_SESSION["ip_info"]["country"] ?? null
        ]);
        $userManager->newUser($new_user);
        $sendMail = new \Matcha\Lib\Mail($data["email"]);
        $sendMail->OAuthRegistrationMail($data["email"], $clearpwd);
    }
}
