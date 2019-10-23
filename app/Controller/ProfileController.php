<?php
namespace Matcha\Controller;

use Matcha\Core\Controller;
use Matcha\Lib\Alert;
use Matcha\Lib\Helper;

class ProfileController extends Controller
{
	private $_myUserData;
	private $_myUserPictures;
	private $_userManager;
	private $_imageManager;
	private $_likeManager;
	private $_matchManager;
	private $_notificationManager;

	public function __construct() {
		parent::__construct();
		$this->checkAccess("You need to be logged in to accces this page.", URL . "login");
		$this->_userManager = new \Matcha\Model\UserManager();
		$this->_imageManager = new \Matcha\Model\ImageManager();
		$this->_likeManager = new \Matcha\Model\LikeManager();
		$this->_matchManager = new \Matcha\Model\MatchManager();
		$this->_notificationManager = new \Matcha\Model\NotificationManager();
		$this->_myUserData = $this->fetchUserData($_SESSION["user_username"]);
		$this->_myUserPictures = array_filter((array)$this->_imageManager->fetchUserPictures($this->_myUserData));
	}

	public function index($username = null) {
		$this->v($username);
	}

	public function edit() {
		$array = $this->renderProfileData($this->_myUserData);
		$this->set($array);
		$this->render("edit");
	}

	public function v($username = null) {
		if (empty($username) && $this->_isLoggedIn) {
			$username = $_SESSION["user_username"];
			$this->redirect(URL . "profile/v/" . $username);
		} else
			$username = $this->secureInput($username);
		if ($this->_userManager->existsUser($username, null) === 0) {
			Alert::danger_alert("The user you requested does not exist.");
			$this->redirect(URL);
		}
		$userData = $this->fetchUserData($username);
		if ($userData->get_usr_active() == 0) {
			Alert::danger_alert("The user's account you requested is not active.", URL);
			$this->redirect(URL);
		}
		if ($this->_userManager->checkBlock($userData->get_usr_id(), $this->_myUserData->get_usr_id())) {
			Alert::danger_alert("You can't view this profile.");
			$this->redirect(URL);
		}
		$array = $this->renderProfileData($userData);
		if ($userData->get_usr_login() != $_SESSION["user_username"])
			$this->processVisit($userData);
		$this->set($array);
		return ($this->render("index"));
	}

	private function renderProfileData($userData) {
		$gender_list = [
			"Male" => '<i class="fas fa-2x fa-fw fa-mars"></i>',
			"Female" => '<i class="fas fa-2x fa-fw fa-venus"></i>'];
		foreach ($gender_list as $key => $value) {
			$genders[] = ["text" => $key, "icon" => $value];
		}
		$array = [
			"userid" => $userData->get_usr_id(),
			"username" => $userData->get_usr_login(),
			"name" => $userData->get_usr_name(),
			"dob" => $userData->get_usr_dob(),
			"age" => !empty($userData->get_usr_dob()) ? date_diff(date_create($userData->get_usr_dob()), date_create(date("Y-m-d")))->y : null,
			"bio" => $userData->get_usr_bio(),
			"profilepic" => $userData->get_usr_ppic(),
			"userpictures" => $this->_imageManager->fetchUserPictures($userData),
			"orientation" => ($userData->get_usr_orientation() == "0") ? "Men" : (($userData->get_usr_orientation() == "1")  ? "Women" : "Men and women"),
			"gender" => $genders[$userData->get_usr_gender()],
			"tags" => $this->_userManager->fetchUserTags($userData->get_usr_id()),
			"rating" => $userData->get_usr_rating(),
			"ratingc" => "dark",
			"geoloc" => $userData->get_usr_geoconsent(),
			"location" => [
				"lat" => $userData->get_usr_lat(),
				"long" => $userData->get_usr_long(),
				"city" => $userData->get_usr_city(),
				"country" => $userData->get_usr_country(),
				"flag" => str_replace(' ', '-', strtolower($userData->get_usr_country()))
			],
			"lastseen" => Helper::time_elapsed_string($userData->get_usr_lastseen()),
			"isonline" => boolval((time() - strtotime($userData->get_usr_lastseen())) < 600),
			"membersince" => $userData->get_usr_regdate(),
			"likedme" => (int)$this->_likeManager->checkLike($userData->get_usr_id(), $this->_myUserData->get_usr_id()),
			"liked" => (int)$this->_likeManager->checkLike($this->_myUserData->get_usr_id(), $userData->get_usr_id()),
			"blockedme" => boolval($this->_userManager->checkBlock($userData->get_usr_id(), $this->_myUserData->get_usr_id())),
			"blocked" => boolval($this->_userManager->checkBlock($this->_myUserData->get_usr_id(), $userData->get_usr_id())),
			"recently_liked" => (int)$this->_likeManager->checkRecentLike($this->_myUserData->get_usr_id(), $userData->get_usr_id()),
			"match" => (int)$this->_matchManager->checkMatch($this->_myUserData->get_usr_id(), $userData->get_usr_id()),
			"unmatch" => (int)$this->_matchManager->checkUnmatch($this->_myUserData->get_usr_id(), $userData->get_usr_id()),
		];
		if ($array["rating"] <= 50)
			$array["ratingc"] = "danger";
		if ($array["rating"] >= 200)
			$array["ratingc"] = "success";
		if ($array["rating"] >= 300)
			$array["ratingc"] = "dark bg-purple";
		return ($array);
	}

	public function uploadUserPicture() {
		if ($this->isAjaxRequest()) {
			$upload = new \Matcha\Lib\ImageUpload();
			if (count($this->_myUserPictures) < 5) {
				$ajax_response = $upload->ajaxUpload($this->_myUserData->get_usr_id());
				if (count($this->_myUserPictures) === 0)
					$this->_userManager->firstUserPicture($this->_myUserData, $upload->img_name);
				else
					$this->_imageManager->addUserPicture($this->_myUserData->get_usr_id(), "pic_" . strval(count($this->_myUserPictures)), $upload->img_name);
				Alert::success_alert("Your picture was successfully uploaded.");
				exit ($ajax_response);
			} else
				exit (json_encode(
					["status" => false,
					"error" => "You can't add more than 5 pictures."]
				));
		} else
			$this->redirect(URL);
	}

	public function makeProfilePicture(string $picn = null) {
		if (!empty($picn)) {
			if (in_array($picn, array_keys($this->_myUserPictures))) {
				$picn = $this->secureInput($picn);
				$this->_imageManager->updateProfilePicture($this->_myUserData, "pic_" . $picn);
				Alert::success_alert("Your profile picture has been changed.");
				$this->redirect(URL . "profile/v/" . $this->_myUserData->get_usr_login());
			} else {
				Alert::danger_alert("Invalid picture number.");
				$this->redirect(URL . "profile/v/" . $this->_myUserData->get_usr_login());
			}
		} else
			$this->redirect(URL);
	}

	public function deleteUserPicture(string $picn = null) {
		if (!empty($picn)) {
			if (in_array($picn, array_keys($this->_myUserPictures))) {
				$picn = $this->secureInput($picn);
				$this->_imageManager->deletePicture($this->_myUserData, "pic_" . $picn);
				Alert::success_alert("This picture has been deleted.");
				$this->redirect(URL . "profile/v/" . $this->_myUserData->get_usr_login());
			} else {
				Alert::danger_alert("Invalid picture number.");
				$this->redirect(URL . "profile/v/" . $this->_myUserData->get_usr_login());
			}
		} else
			$this->redirect(URL);
	}

	public function processEdit() {
		if ($this->isAjaxRequest()) {
			if (!empty($_POST)) {
				$tagsdata = $this->handleTags($_POST["tags"]);	
				unset($_POST["tags"]);
				$this->secureForm($_POST);
				$namelen = strlen($_POST["displayname"]);
				$tagscount = count($tagsdata);
				$dobcheck = explode('-', $_POST["date_birth"]);
				$genderval = intval($_POST["gender"]);
				$orientationval = intval($_POST["sexual"]);
				$biolen = strlen($_POST["bio"]);
				if (($namelen >= 2 && $namelen <= 32)
					&& ($tagscount >= 2 && $tagscount <= 20)
					&& ($dobcheck[0] >= 1900 && $dobcheck[0] <= 2001)
					&& ($genderval == 0 || $genderval == 1)
					&& ($orientationval == 0 || $orientationval == 1 || $orientationval == 2)
					&& ($biolen >= 20 && $biolen <= 3000)) {
					$profilename = $_POST["displayname"];
					$profiletags = implode(", ", $tagsdata);
					$profiledob = $_POST["date_birth"];
					$profilegender = $_POST["gender"]; 
					$profileorientation = $_POST["sexual"];
					$profilebio = $_POST["bio"]; 
				} else
					exit (json_encode(["status" => false]));
				$editProfile = new \Matcha\Model\User([
					"usr_id" => $this->_myUserData->get_usr_id(),
					"usr_login" => $this->_myUserData->get_usr_login(),
					"usr_name" => $profilename,
					"usr_tags" => $profiletags,
					"usr_dob" => $profiledob,
					"usr_gender" => $profilegender,
					"usr_orientation" => $profileorientation,
					"usr_bio"	=> $profilebio,
				]);
				if ($this->_userManager->editProfile($editProfile)) {
					Alert::success_alert("Your profile information has been updated.");
					exit (json_encode(["status" => true]));
				} else
					exit (json_encode(["status" => false]));
			} else
				exit (json_encode(["status" => false]));
		} else
			$this->redirect(URL);
	}

	private function processVisit($userData) {
		if (!empty($userData)) {
			$visiteeId = $userData->get_usr_id();
			$visiteeUname = $userData->get_usr_login();
			$visitManager = new \Matcha\Model\VisitManager();
			$visitManager->updateLastVisit($this->_myUserData->get_usr_id(), $visiteeId, 1);
			$recentVisit = $visitManager->checkRecentVisit($this->_myUserData->get_usr_id(), $visiteeId);
			if (!$recentVisit) {
				$visitManager->newVisit($this->_myUserData->get_usr_id(), $visiteeId, 1);
				$this->_userManager->updateUserRating($visiteeId, 10);
				$this->_notificationManager->newNotification($this->_myUserData->get_usr_id(), $visiteeId, 1);
				if ($userData->get_usr_vst_sendmail() == 1) {
					$sendMail = new \Matcha\Lib\Mail($userData->get_usr_email());
					$sendMail->newVisitMail($this->_myUserData->get_usr_login());
				}
			}
			return;
		}
		else {
			Alert::danger_alert("There was a problem with the data you submitted.");
			$this->redirect(URL);
		}
	}

	public function processLike() {
		if ($this->isAjaxRequest()) {
			if (!empty($_POST)) {
				$this->secureForm($_POST);
				$likedUname = $_POST["receiver_username"];
				if ($likedUname != $_SESSION["user_username"]) {
					$likedUser = $this->fetchUserData($likedUname);
					$likedId = $likedUser->get_usr_id();
					$likeData = $this->_likeManager->fetchLikeData($this->_myUserData->get_usr_id(), $likedId);
					if (empty($likeData)
						|| (!empty($likeData) && $likeData->lik_active == 0)) {
						$this->_likeManager->newLike($this->_myUserData->get_usr_id(), $likedId);
						if (empty($likeData)
							|| (!empty($likeData) && (time() - strtotime($likeData->lik_date) >= 259200))) {
							$this->_userManager->updateUserRating($likedId, 20);
							$this->_notificationManager->newNotification($this->_myUserData->get_usr_id(), $likedId, 0);
							if ($likedUser->get_usr_lik_sendmail() == 1) {
								$sendMail = new \Matcha\Lib\Mail($likedUser->get_usr_email());
								$sendMail->newLikeMail($this->_myUserData->get_usr_login());
							}
						}
					}
					if ($match = $this->handleMatch($this->_myUserData->get_usr_id(), $likedId)) {
						$this->_notificationManager->newNotification($this->_myUserData->get_usr_id(), $likedId, (int)$match);
						$this->_notificationManager->newNotification($likedId, $this->_myUserData->get_usr_id(), (int)$match);
					}
					exit (json_encode([
						"status" => true, 
						]));
				} else
					exit (json_encode([
						"status" => false, 
						"message" => "You can't like yourself."
						]));
			} else
				exit (json_encode([
					"status" => false
					]));
		} else
			$this->redirect(URL);
	}

	private function handleMatch($user_1, $user_2) {
		$validateMatch = boolval($this->_matchManager->checkIfLikesMatch($user_1, $user_2) == 2);
		if ($validateMatch)
			$messageManager = new \Matcha\Model\MessageManager;
		if ($validateMatch && !$this->_matchManager->checkMatch($user_1, $user_2)) {
			$match = ($this->_matchManager->newMatch($user_1, $user_2)) + 2;
			$messageManager->newUserConversation($user_1, $user_2);
			$this->_userManager->updateUserRating($user_1, 50);
			$this->_userManager->updateUserRating($user_2, 50);
		}
		else if ($validateMatch && $this->_matchManager->checkMatch($user_1, $user_2)) {
			$match = ($this->_matchManager->deleteMatch($user_1, $user_2)) + 3;
			$messageManager->deleteUserConversation($user_1, $user_2);
			$this->_userManager->updateUserRating($user_2, -20);
		}
		return ($match ?? null);
	}

	private function handleTags($input_tags) {
		$tagManager = new \Matcha\Model\TagManager();
		foreach ($input_tags as &$value)
			$this->secureForm($value);
		$uniqueIds = array_map(function ($i) {
			if ($i["id"] == "false")
				$i["id"] .= bin2hex(random_bytes(2));
			return $i["id"];
			}, $input_tags);
		$uniqueNames = array_unique(array_map(function ($i) { return $i["name"]; }, $input_tags));
		$input_tags = array_combine($uniqueIds, $uniqueNames);
		$parsed_tags = [];
		$tags = [];
		foreach($input_tags as $key => $value)
			$parsed_tags[] = ["id" => strval($key), "name" => $value];
		foreach ($parsed_tags as &$value) {
			$this->secureForm($value);
			$value["name"] = preg_replace("/[^a-zA-Z]/", '', $value["name"]);
			if (strstr($value["id"], "false"))
				$value["id"] = $tagManager->newTag($_SESSION["user_id"], $value["name"])->tag_id;
			$tags[] = $value["id"];
		}
		return ($tags);
	}

	public function processTagSearch() {
		if ($this->isAjaxRequest()) {
			if (!empty($_POST)) {
				$this->secureForm($_POST);
				$tagManager = new \Matcha\Model\TagManager();
				$data = $tagManager->searchTags('%' . $_POST["tags"] . '%');
				exit (json_encode([
					"status" => true,
					"tags" => (!empty($data) ? $data : null)
					]));
			} else
				exit (["status" => false]);
		} else
			$this->redirect(URL);
	}

	public function processGeolocation() {
		if ($this->isAjaxRequest()) {
			if (!empty($_POST) && ($_POST["geo"] == "0" || $_POST["geo"] == "1")) {
				$this->secureForm($_POST["userpos"]);
				$userpos = $_POST["userpos"];
				unset($_POST["userpos"]);
				$this->secureForm($_POST);
				$usercity = $_POST["usercity"];
				$usercountry = $_POST["usercountry"];
				$geo = intval($_POST["geo"]);
				$this->_userManager->updateGeolocationConsent($this->_myUserData, $geo);
				$this->_userManager->editLocation($this->_myUserData, $userpos[0], $userpos[1], $usercity, $usercountry);
				exit (true);
			} else {
				$this->_userManager->updateGeolocationConsent($this->_myUserData, 0);
				exit (false);
			}
		}
	}

	public function processBlock(string $username = null) {
		if (!empty($username)) {
			$userData = $this->fetchUserData($username);
			$blockData = $this->_userManager->fetchBlockData($this->_myUserData->get_usr_id(), $userData->get_usr_id());
			if (empty($blockData)
				|| (!empty($blockData) && $blockData->blo_active == 0)) {
				$messageManager = new \Matcha\Model\MessageManager();
				$this->_userManager->removeLike($this->_myUserData->get_usr_id(), $userData->get_usr_id());
				$this->_userManager->toggleBlock($this->_myUserData->get_usr_id(), $userData->get_usr_id(), 1);
				if ($this->_matchManager->deleteMatch($this->_myUserData->get_usr_id(), $userData->get_usr_id()))
					$this->_notificationManager->newNotification($this->_myUserData->get_usr_id(), $userData->get_usr_id(), 4);
				$messageManager->deleteUserConversation($this->_myUserData->get_usr_id(), $userData->get_usr_id());
				if (empty($blockData)
					|| (!empty($blockData) && (time() - strtotime($blockData->blo_date) >= 604800))) {
					$this->_userManager->updateUserRating($userData->get_usr_id(), -30);
				}
				Alert::warning_alert("You have blocked this user.");
			} else if (!empty($blockData) && $blockData->blo_active == 1) {
				$this->_userManager->toggleBlock($this->_myUserData->get_usr_id(), $userData->get_usr_id(), 0);
				Alert::success_alert("You have unblocked this user.");
			}
			$this->redirect(URL . "profile/v/" . $userData->get_usr_login());
		} else
			$this->redirect(URL);
	}
}