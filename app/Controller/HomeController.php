<?php

namespace Matcha\Controller;

use Matcha\Core\Controller;

class HomeController extends Controller
{
    private $_myUserData;
    private $_userManager;

    public function __construct()
    {
        parent::__construct();
        if ($this->_isLoggedIn) {
            $this->_userManager = new \Matcha\Model\UserManager();
            $this->_myUserData = $this->_userManager->fetchUserData($_SESSION['user_username']);
            if (!isset($_SESSION["smart_filter"]))
                $_SESSION["smart_filter"] = true;
        }
    }

    public function index()
    {
        if (!file_exists(APP . "config/installed"))
            $this->redirect(URL . "setup");
        if ($this->_isLoggedIn) {
            $data = $this->renderMemberData();
            if ($this->isAjaxRequest() && !empty($_POST)) {
                unset($data["suggestions"]);
                $data["suggestions"] = $this->renderMatchSuggestions();
            }
            $this->set($data);
            return ($this->render("members"));
        } else
            return ($this->render("index"));
    }

    private function renderMemberData()
    {
        $user_rating = $this->_myUserData->get_usr_rating();
        $user_age = !empty($this->_myUserData->get_usr_dob()) ? date_diff(date_create($this->_myUserData->get_usr_dob()), date_create(date("Y-m-d")))->y : null;
        $rating_c = "text-dark";
        if ($user_rating <= 50)
            $rating_c = "text-danger";
        if ($user_rating >= 200)
            $rating_c = "text-success";
        if ($user_rating >= 300)
            $rating_c = "purple";
        $ratinghint = "
			<ul class='list-unstyled fa-xs text-left'>
				How does my rating change?
				<li class='nav-item mt-1'><i class='fas fa-sm fa-plus green'></i> 10 points for getting a visit</li>
				<li class='nav-item'><i class='fas fa-sm fa-plus green'></i> 20 points for getting a like</li>
				<li class='nav-item'><i class='fas fa-sm fa-plus green'></i> 20 points for starting a conversation (10 points for the other user)</li>
				<li class='nav-item mb-2'><i class='fas fa-sm fa-plus green'></i> 50 points for a match (for both users)</li>
				<li class='nav-item'><i class='fas fa-sm fa-minus red'></i> 20 points for getting unmatched</li>
				<li class='nav-item'><i class='fas fa-sm fa-minus red'></i> 30 points for getting blocked</li>
			</ul>";

        $visitManager = new \Matcha\Model\VisitManager();
        $lastVisits = $visitManager->fetchVisits($this->_myUserData->get_usr_id());
        foreach ($lastVisits as $value)
            $value->{"isonline"} = boolval((time() - strtotime($value->usr_lastseen)) < 600);

        $searchManager = new \Matcha\Model\SearchManager();
        $matchSuggestions = $searchManager->fetchMatchSuggestions($this->_myUserData, $_SESSION["custom_filter_params"] ?? null);
        $array = [];
        if (!empty($_SESSION["custom_filter_params"]["tags"])) {
            foreach ($matchSuggestions as $key => $value) {
                if (count(array_intersect(explode(', ', $this->_myUserData->get_usr_tags()), explode(', ', $value->usr_tags))) == $_SESSION["custom_filter_params"]["tags"])
                    $array[] = $matchSuggestions[$key];
            }
            unset($matchSuggestions);
            $matchSuggestions = $array;
        }
        foreach ($matchSuggestions as $value) {
            $value->{"isonline"} = boolval((time() - strtotime($value->usr_lastseen)) < 600);
            $value->{"tags"} = $this->_userManager->fetchUserTags($value->usr_id);
            $value->{"common_tags"} = count(array_intersect(explode(', ', $this->_myUserData->get_usr_tags()), explode(', ', $value->usr_tags)));
        }
        return ([
            "lastvisits" => $lastVisits,
            "user_rating" => $user_rating,
            "user_tags" => array_map(function ($i) {
                return $i->tag_id;
            }, $this->_userManager->fetchUserTags($_SESSION["user_id"])),
            "user_age" => $user_age,
            "rating_c" => $rating_c,
            "ratinghint" => $ratinghint,
            "suggestions" => $matchSuggestions,
        ]);
    }

    private function renderMatchSuggestions()
    {
        if (!empty($_POST)) {
            $this->secureForm($_POST["f_age"]);
            $this->secureForm($_POST["f_rating"]);
            $_POST["f_distance"] = $this->secureInput($_POST["f_distance"]);
            $_POST["f_tags"] = $this->secureInput($_POST["f_tags"]);
            $filter_params = [
                "age" => array_map('intval', $_POST["f_age"]),
                "distance" => $_POST["f_distance"] == 6000 ? 40000 : intval($_POST["f_distance"]),
                "rating" => [
                    (int)abs($_POST["f_rating"][0] - $this->_myUserData->get_usr_rating()),
                    (int)abs($_POST["f_rating"][2] - $this->_myUserData->get_usr_rating())
                ],
                "tags" => intval($_POST["f_tags"])
            ];
            $_SESSION["custom_filter_params"] = $filter_params;
            $searchManager = new \Matcha\Model\SearchManager();
            $data = $searchManager->fetchMatchSuggestions($this->_myUserData, $filter_params);
            $array = [];
            foreach ($data as $value) {
                $value->{"isonline"} = boolval((time() - strtotime($value->usr_lastseen)) < 600);
                $value->{"tags"} = $this->_userManager->fetchUserTags($value->usr_id);
                $value->{"common_tags"} = count(array_intersect(explode(', ', $this->_myUserData->get_usr_tags()), explode(', ', $value->usr_tags)));
            }
            if (!empty($filter_params["tags"])) {
                foreach ($data as $key => $value) {
                    if (count(array_intersect(explode(', ', $this->_myUserData->get_usr_tags()), explode(', ', $value->usr_tags))) == $filter_params["tags"])
                        $array[] = $data[$key];
                }
                return ($array);
            }
            return ($data);
        }
    }

    public function userSmartFilterToggle()
    {
        if ($this->isAjaxRequest()) {
            $_SESSION["smart_filter"] = !$_SESSION["smart_filter"];
            if ($_SESSION["smart_filter"] === true)
                unset($_SESSION["custom_filter_params"]);
        } else
            $this->redirect(URL);
    }
}
