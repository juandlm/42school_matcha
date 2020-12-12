<?php

namespace Matcha\Controller;

use Matcha\Core\Controller;

class SearchController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        if ($this->_isLoggedIn) {
            $this->_userManager = new \Matcha\Model\UserManager();
            $this->_myUserData = $this->_userManager->fetchUserData($_SESSION['user_username']);
        }
    }

    public function index()
    {
        $user_rating = $this->_myUserData->get_usr_rating();
        $user_age = !empty($this->_myUserData->get_usr_dob()) ? date_diff(date_create($this->_myUserData->get_usr_dob()), date_create(date("Y-m-d")))->y : null;
        $this->set([
            "user_rating" => $user_rating,
            "user_tags" => array_map(function ($i) {
                return $i->tag_id;
            }, $this->_userManager->fetchUserTags($_SESSION["user_id"])),
            "user_age" => $user_age,
        ]);
        return ($this->render('index'));
    }

    public function doUserSearch()
    {
        if ($this->isAjaxRequest()) {
            if (!empty($_POST)) {
                $this->secureForm($_POST["s_age"]);
                $this->secureForm($_POST["s_rating"]);
                $_POST["s_distance"] = $this->secureInput($_POST["s_distance"]);
                $_POST["s_tags"] = $this->secureInput($_POST["s_tags"]);
                if (!empty($_POST["s_query"]))
                    $query = $this->secureInput($_POST["s_query"]);
                $filter_params = [
                    "age" => array_map('intval', $_POST["s_age"]),
                    "distance" => $_POST["s_distance"] == 6000 ? 40000 : intval($_POST["s_distance"]),
                    "rating" => [
                        (int)abs($_POST["s_rating"][0] - $this->_myUserData->get_usr_rating()),
                        (int)abs($_POST["s_rating"][2] - $this->_myUserData->get_usr_rating())
                    ],
                    "tags" => intval($_POST["s_tags"])
                ];
                $_SESSION["custom_search_params"] = $filter_params;
                $searchManager = new \Matcha\Model\SearchManager();
                $searchResults = $searchManager->fetchMatchSuggestions($this->_myUserData, $filter_params, $query ?? null);
                $array = [];
                if (!empty($filter_params["tags"])) {
                    foreach ($searchResults as $key => $value) {
                        if (count(array_intersect(explode(', ', $this->_myUserData->get_usr_tags()), explode(', ', $value->usr_tags))) == $_SESSION["custom_search_params"]["tags"])
                            $array[] = $searchResults[$key];
                    }
                    unset($searchResults);
                    $searchResults = $array;
                }
                foreach ($searchResults as $value) {
                    $value->{"isonline"} = boolval((time() - strtotime($value->usr_lastseen)) < 600);
                    $value->{"tags"} = $this->_userManager->fetchUserTags($value->usr_id);
                    $value->{"common_tags"} = count(array_intersect(explode(', ', $this->_myUserData->get_usr_tags()), explode(', ', $value->usr_tags)));
                }
                exit(json_encode([
                    "status" => true,
                    "results" => $searchResults
                ]));
            } else
                exit(json_encode([
                    "status" => false
                ]));
        } else
            $this->redirect(URL);
    }
}
