<?php

namespace Matcha\Controller;

use Matcha\Core\Controller;

class MessagesController extends Controller
{
    private $_myUserData;
    private $_userManager;
    private $_messageManager;
    private $_matchManager;
    private $_myConversations;
    private $_myMatches;

    public function __construct()
    {
        parent::__construct();
        $this->checkAccess("You need to be logged in to accces this page.", URL . "login");
        $this->_myUserData = $this->fetchUserData($_SESSION["user_username"]);
        $this->_userManager = new \Matcha\Model\UserManager();
        $this->_messageManager = new \Matcha\Model\MessageManager();
        $this->_matchManager = new \Matcha\Model\MatchManager();
        $this->_myConversations = $this->_messageManager->fetchUserConversations($this->_myUserData->get_usr_id());
        $this->_myMatches = $this->_matchManager->fetchUserMatches($this->_myUserData->get_usr_id());
        if (!isset($_SESSION["msg_show_matches"]))
            $_SESSION["msg_show_matches"] = false;
    }

    public function index($username = null)
    {
        if (empty($username)) {
            $conversationData = $this->renderConversationData(null, $this->_myConversations, null);
            $conversationData["matches"] = $this->_myMatches;
            if ($this->isAjaxRequest())
                exit(json_encode($conversationData = ["status" => true] + $conversationData));
            else {
                $this->set($conversationData);
                return ($this->render("index"));
            }
        } else
            $this->redirect(URL . "messages");
    }

    public function t(string $username = null)
    {
        if (
            empty($username)
            || (!empty($username) && $username == $_SESSION['user_username'])
        )
            $this->redirect(URL . "messages");

        $userData = $this->fetchUserData($this->secureInput($username));
        $username = $userData->get_usr_login();
        if ($con_id = $this->checkMessageAuth($username)) {
            $conversationData = $this->renderConversationData($username, $this->_myConversations, $this->_messageManager->fetchConversationMessages($con_id));
            $conversationData["matches"] = $this->_myMatches;
            $conversationData["user_data"] = [
                "username" => $username,
                "userppic" => $userData->get_usr_ppic(),
                "userdisplayname" => $userData->get_usr_name(),
                "match_info" => $this->_matchManager->fetchMatchData($this->_myUserData->get_usr_id(), $userData->get_usr_id())
            ];
            $this->_messageManager->clearUnseenUserMessages($con_id, $_SESSION["user_id"]);
            if ($this->isAjaxRequest())
                exit(json_encode($conversationData = ["status" => true] + $conversationData));
            else {
                $this->set($conversationData);
                return ($this->render("index"));
            }
        } elseif ($this->isAjaxRequest()) {
            exit(json_encode(["status" => false]));
        } else
            $this->redirect(URL . "messages");
    }

    private function renderConversationData($username, $conversations, $messages): array
    {
        $today = new \DateTime("today");
        foreach ($conversations as $key => $value) {
            $contact_data[$key]["msg"] = isset($value->msg_body, $value->msg_date, $value->msg_seen, $value->msg_usr_id_from);
            if ($value->con_usr_id_1 == $_SESSION["user_id"])
                unset($value->con_usr_id_1, $value->con_usr_login_1, $value->con_usr_name_1, $value->con_usr_ppic_1);
            if (!empty($username) && array_search($username, (array)$value))
                $value->selected = "c_active";
            if ($contact_data[$key]["msg"]) {
                $cmp_date = \DateTime::createFromFormat("Y-m-d H:i:s", $value->msg_date);
                $cmp_date->setTime(0, 0, 0);
                $diff = $today->diff($cmp_date);
                switch ($diffDays = (int)$diff->format("%R%a")) {
                    case 0:
                        $value->lmsg_format = "H:i";
                        break;
                    case ($diffDays <= -1):
                        $value->lmsg_format = "D";
                        break;
                    case ($diffDays <= -7):
                        $value->lmsg_format = "M d";
                        break;
                }
            }

            $contact_data[$key]["id"] = ($value->con_usr_id_1 ?? $value->con_usr_id_2);
            $contact_data[$key]["username"] = ($value->con_usr_login_1 ?? $value->con_usr_login_2);
            $contact_data[$key]["name"] = ($value->con_usr_name_1 ?? $value->con_usr_name_2);
            $contact_data[$key]["ppic"] = ($value->con_usr_ppic_1 ?? $value->con_usr_ppic_2);
            $conversation_list[$key] = '<div class="con_wrapper position-relative ' . ($contact_data[$key]["msg"] == false ? "d-none" : '') . '">';
            $conversation_list[$key] .= '<a tabindex="0" class="contact-popover position-absolute text-dark" role="button" data-content="<div class=\'list-group border-0\'><a href=\'' . URL . 'profile/v/' . $contact_data[$key]["username"] . '\' class=\'border-0 list-group-item list-group-item-action\'>View profile</a></div>"><i class="fas fa-ellipsis-h"></i></a>';
            $conversation_list[$key] .= '<div class="chat_list">';
            $conversation_list[$key] .= '<a href="' . URL . "messages/t/" . $contact_data[$key]["username"] . '" class="conversation-item d-flex text-dark p-3 border-bottom ' . ($value->selected ?? '') . '">';
            $conversation_list[$key] .= '<div class="picture pr-2">';
            $conversation_list[$key] .= '<svg viewBox="0 0 1 1" width="50px" height="50px" class="rounded-circle img-thumbnail">';
            $conversation_list[$key] .= '<image id="pPicImg" xlink:href="' . (stristr($contact_data[$key]["ppic"], "http") ? $contact_data[$key]["ppic"] :  URL . "assets/userphotos/" . $contact_data[$key]["ppic"]) . '" width="100%" height="100%" preserveAspectRatio="xMidYMid slice"/>';
            $conversation_list[$key] .= '</svg></div>';
            $conversation_list[$key] .= '<div class="d-flex flex-column flex-grow-1' . ($contact_data[$key]["msg"] && $value->msg_seen == 0 && $value->msg_usr_id_from !=  $_SESSION["user_id"] ? " font-weight-bold " : '') . '">';
            $conversation_list[$key] .= $contact_data[$key]["name"];
            if ($contact_data[$key]["msg"]) {
                $conversation_list[$key] .= '<div class="gray d-inline-flex align-items-center">';
                $conversation_list[$key] .= '<span class="last-message">' . $value->msg_body . '</span>';
                $conversation_list[$key] .= '<span class="mx-1">·</span>';
                $conversation_list[$key] .= '<small>' . date($value->lmsg_format, strtotime($value->msg_date)) . '</small></div>';
            }
            $conversation_list[$key] .= '</div>';
            if ($contact_data[$key]["msg"] && $value->msg_seen == 0 && $value->msg_usr_id_from !=  $_SESSION["user_id"])
                $conversation_list[$key] .= '<div class="unread-message align-self-center"><i class="fas fa-circle text-matcha"></i></div>';
            $conversation_list[$key] .= '</a></div></div>';
        }
        if (!empty($messages)) {
            $message_list = '';
            $group_date = '';
            foreach ($messages as $value) {
                if ($group_date != $value->msg_when) {
                    $group_date = $value->msg_when;
                    $message_list .= '<div class="d-inline-block w-100 text-center my-2"><span class="badge badge-secondary">' . $group_date . '</span></div>';
                }
                $message_list .= '<div class="' . ($value->msg_usr_id_from == $_SESSION["user_id"] ? 'sent' : 'received') . '">';
                $message_list .= '<p>' . $value->msg_body . '<span class="message-time">' . date("H:i", strtotime($value->msg_date)) . '</span></p></div>';
            }
        }
        return ([
            "conversations" => $conversation_list ?? null,
            "contact_data" => $contact_data ?? null,
            "messages" => $message_list ?? null,
            "group_date" => $group_date ?? null
        ]);
    }

    private function checkMessageAuth($data)
    {
        foreach ($this->_myConversations as $value) {
            if (
                array_search($data, (array)$value, true) == "con_usr_login_1"
                || array_search($data, (array)$value, true) == "con_usr_login_2"
            )
                $con_id = $value->con_id;
        }
        return ($con_id ?? null);
    }

    public function addMessage()
    {
        if ($this->isAjaxRequest()) {
            if (!empty($_POST)) {
                $this->secureForm($_POST);
                $message = [
                    "con_login" => $_POST["con_login"],
                    "sender" => $_SESSION["user_id"],
                    "msg_body" => $_POST["msg_body"]
                ];
                $userData = $this->fetchUserData($message["con_login"]);
                if ($message["con_id"] = $this->checkMessageAuth($userData->get_usr_login())) {
                    if (empty($this->_messageManager->fetchConversationMessages($message["con_id"]))) {
                        $notificationManager = new \Matcha\Model\NotificationManager;
                        $notificationManager->newNotification($this->_myUserData->get_usr_id(), $userData->get_usr_id(), 2);
                        $this->_userManager->updateUserRating($this->_myUserData->get_usr_id(), 20);
                        $this->_userManager->updateUserRating($userData->get_usr_id(), 10);
                        if ($userData->get_usr_msg_sendmail() == 1) {
                            $sendMail = new \Matcha\Lib\Mail($userData->get_usr_email());
                            $sendMail->newMessageMail($this->_myUserData->get_usr_login(), $message["msg_body"]);
                        }
                    }
                    if ($this->_messageManager->newUserMessage($message))
                        exit(json_encode([
                            "status" => true,
                            "input_result" => $message
                        ]));
                } else
                    exit(json_encode(["status" => false]));
            } else
                exit(json_encode(["status" => false]));
        } else
            $this->redirect(URL);
    }

    public function userShowMatchesToggle()
    {
        if ($this->isAjaxRequest())
            $_SESSION["msg_show_matches"] = !$_SESSION["msg_show_matches"];
        else
            $this->redirect(URL);
    }
}
