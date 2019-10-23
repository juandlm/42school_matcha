<?php
namespace Matcha\Controller;

use Matcha\Core\Controller;
use Matcha\Lib\Alert;

class AccountController extends Controller
{
	private $_myUserData;
	private $_userManager;

	public function __construct() {
		parent::__construct();
		$this->checkAccess("You need to be logged in to accces this page.", URL . "login");
		$this->_userManager = new \Matcha\Model\UserManager();
		$this->_myUserData = $this->fetchUserData($_SESSION['user_username']);
	}

	public function index() {
		return ($this->render("index"));
	}

	public function emailPreferences() {
		$emailPref["likes"] = $this->_myUserData->get_usr_lik_sendmail();
		$emailPref["messages"] = $this->_myUserData->get_usr_msg_sendmail();
		$emailPref["visits"] = $this->_myUserData->get_usr_vst_sendmail();
		$this->set($emailPref);
		return ($this->render("emailpreferences"));
	}

	public function edit() {
		return ($this->render("edit"));
	}

	public function deactivate() {
		return ($this->render("deactivate"));
	}

	public function blocking() {
		$blockedusers = $this->_userManager->fetchBlockedUsers($this->_myUserData->get_usr_id());
		$this->set(["blockedusers" => $blockedusers]);
		return ($this->render("blocking"));
	}

	public function processPreferences() {
		$this->secureForm($_POST);
		$plikes = (int)(!empty($_POST["plikes"]) && $_POST["plikes"] == "on");
		$pmessages = (int)(!empty($_POST["pmessages"]) && $_POST["pmessages"] == "on");
		$pvisits = (int)(!empty($_POST["pvisits"]) && $_POST["pvisits"] == "on");
		if (($this->_myUserData->get_usr_lik_sendmail() != $plikes)
			|| $this->_myUserData->get_usr_msg_sendmail() != $pmessages
			|| $this->_myUserData->get_usr_vst_sendmail() != $pvisits) {
			$editPreferences = new \Matcha\Model\User([
				"usr_id" => $_SESSION["user_id"],
				"usr_lik_sendmail" => $plikes,
				"usr_msg_sendmail" => $pmessages,
				"usr_vst_sendmail" => $pvisits]);
			$this->_userManager->editPreferences($editPreferences);
			Alert::success_alert("Your email preferences were successfully changed.");
			$this->redirect("emailpreferences");
		} else {
			Alert::warning_alert("You didn't change anything in your email preferences.");
			$this->redirect("emailpreferences");
		}
	}

	public function processEdit() {
		$newpassword = null;
		$newusername = null;
		$newemail = null;
		if (!empty($_POST)) {
			$this->secureForm($_POST);
			if (!empty($_POST["password"])) {
				$oldpassword = $_POST["password"];
				if (password_verify($oldpassword, $this->_myUserData->get_usr_pwd())) {
					if (!empty($_POST["new_password"]) && !empty($_POST["cnew_password"])) {
						if ($_POST["cnew_password"] === $_POST["new_password"]) {
							$newpassword = $_POST["new_password"];
							if (preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[\s\S]{6,16}$/", $newpassword))
								$newpassword = password_hash($newpassword, PASSWORD_DEFAULT);
							else {
								Alert::danger_alert("Passwords must be between 6 and 16 characters long and contain at least one uppercase letter, one lowercase letter and one digit.");
								$this->redirect("edit");
							}
						} else {
							Alert::danger_alert("The new passwords you entered don't match.");
							$this->redirect("edit");
						}
					}
					if (!empty($_POST["new_username"])) {
						if (preg_match("/^[a-z\d_-]{3,20}$/i", $_POST["new_username"])) {
							if ($_POST["new_username"] == $this->_myUserData->get_usr_login()) {
								Alert::danger_alert("This is your current username.");
								$this->redirect("edit");
							}
							if ($this->_userManager->existsUser($_POST["new_username"], null) == 0) {
								$newusername = $_POST["new_username"];
							} else {
								Alert::danger_alert("This username is already in use.");
								$this->redirect("edit");
							}
						} else {
							Alert::danger_alert("Usernames must be between 3 and 20 characters long and can only contain alphanumeric, underscore and hyphen characters.");
							$this->redirect("edit");
						}
					}
					if (!empty($_POST["new_email"])) {
						if (filter_var($_POST["new_email"], FILTER_VALIDATE_EMAIL)) {
							if ($_POST["new_email"] == $this->_myUserData->get_usr_email()) {
								Alert::danger_alert("This is your current email address.");
								$this->redirect("edit");
							}
							if ($this->_userManager->existsUser(null, $_POST["new_email"]) == 0) {
								$newemail = $_POST["new_email"];
							} else {
								Alert::danger_alert("This email address is already in use.");
								$this->redirect("edit");
							}
						} else {
							Alert::danger_alert("The email address you entered is not valid.");
							$this->redirect("edit");
						}
					}
				} else {
					Alert::danger_alert("The current password you entered is incorrect.");
					$this->redirect("edit");
				}
			} else {
				Alert::danger_alert("You didn't enter your current password.");
				$this->redirect("edit");
			}
			if (!empty($newusername))
				$this->_myUserData->set_usr_login($newusername);
			if (!empty($newemail))
				$this->_myUserData->set_usr_email($newemail);
			if (!empty($newpassword))
				$this->_myUserData->set_usr_pwd($newpassword);
			$this->_userManager->editUser($this->_myUserData);
			Alert::success_alert("Your account information was successfully updated. Please log in again.");
			unset($_SESSION["user_username"], $_SESSION["user_id"]);
			session_commit();
			$this->disconnect(URL . "login");
		} else {
			Alert::danger_alert("There was a problem with the data you submitted.");
			$this->redirect("edit");
		}
	}

	public function processDeactivation() {
		if (!empty($_POST)) {
			$this->secureForm($_POST);
			if (!empty($_POST["confirmcheck"])) {
				if (!empty($_POST["password"])) {
					if (password_verify($_POST["password"], $this->_myUserData->get_usr_pwd())) {
						$this->_userManager->deactivateUser($this->_myUserData);
						Alert::warning_alert("Your account is no longer active. Come back soon!");
						unset($_SESSION["user_username"], $_SESSION["user_id"]);
						session_commit();
						$this->disconnect();
					} else {
						Alert::danger_alert("The current password you entered is incorrect.");
						$this->redirect("deactivate");
					}
				} else {
					Alert::danger_alert("You didn't enter your current password.");
					$this->redirect("deactivate");
				}
			} else {
				Alert::danger_alert("You didn't check the box to confirm the deactivation.");
				$this->redirect("deactivate");
			}
		} else {
			Alert::danger_alert("There was a problem with the data you submitted.");
			$this->redirect("deactivate");
		}
	}

	public function fetchUserNotifications() {
		if ($this->isAjaxRequest()) {
			if ($this->_isLoggedIn) {
				$notificationManager = new \Matcha\Model\NotificationManager();
				$messageManager = new \Matcha\Model\MessageManager();
				$allNotifications = $notificationManager->fetchAllNotifications($_SESSION['user_id']);
				$countUnseen = $notificationManager->fetchUnseen($_SESSION['user_id']);
				$unseenConversations = $messageManager->fetchUnseenMessages($_SESSION['user_id']);
				$notificationIcon = [
					0 => '<i class="fas fa-heart fa-fw"></i>',
					1 => '<i class="fas fa-eye fa-fw"></i>',
					2 => '<i class="fas fa-comments fa-fw"></i>',
					3 => '<i class="fas fa-hand-holding-heart fa-fw"></i>',
					4 => '<i class="fas fa-heart-broken fa-fw"></i>'
				];
				$notificationText = [
					0 => "liked you",
					1 => "visited your profile",
					2 => "sent you a message",
					3 => "matched with you!",
					4 => "unmatched you"
				];
				$output = '';
				if (!empty($allNotifications)) {
					foreach ($allNotifications as $value) {
						$output .= '<a href="' . URL . ($value->noti_type == 2 ? 'messages/t/' : 'profile/v/') . $value->noti_usr_login_from . '">';
						if ($value->noti_seen)
							$output .= '<li class="notification-item dropdown-item">';
						else
							$output .= '<li class="notification-item dropdown-item bg-matcha-light">';
						$output .= $notificationIcon[$value->noti_type] . '<small><span class="text-matcha">'. $value->noti_usr_name_from .' </span>' . $notificationText[$value->noti_type] .'</small>';
						$output .= '<div class="notif_time text-muted">' . \Matcha\Lib\Helper::time_elapsed_string($value->noti_date) . '</div></li></a>';
					}
				} else
					$output .= '<li class="dropdown-item"><small class="text-muted">You have no notifications.</small></li>';
				exit (json_encode([
					"status" => true,
					"notifications" => $output,
					"unseen_count" => $countUnseen,
					"unseen_conv" => $unseenConversations,
					]));
			} else
				exit (json_encode([
					"status" => false,
					"message" => "You are not logged in."
					]));
		} else
			$this->redirect(URL);
	}
	
	public function clearNotifications() {
		if ($this->isAjaxRequest()) {
			$notificationManager = new \Matcha\Model\NotificationManager;
			if ($notificationManager->fetchUnseen($_SESSION['user_id'])) {
				if ($notificationManager->clearUserNotifications($_SESSION['user_id']))
					exit (json_encode(["status" => true]));
				else
					exit (json_encode([
						"status" => false,
						"message" => "Your notifications couldn't be cleared."
						]));
			} else
				exit (json_encode(["status" => true]));
		} else
			$this->redirect(URL);
	}

	public function userLastAction() {
		if ($this->isAjaxRequest()) {
			$_SESSION["last_action"] = time();
			$this->_userManager->updateLastSeen($this->_myUserData, $_SESSION['last_action']);
			exit (true);
		} else
			$this->redirect(URL);
	}
}
