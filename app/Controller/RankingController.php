<?php
namespace Matcha\Controller;

use Matcha\Core\Controller;

class RankingController extends Controller
{
    public function index() {
		$userManager = new \Matcha\Model\UserManager;
		$ranking = $userManager->fetchUserRankings();
		$gender_list = [
			"Male" => '<i class="fas fa-2x fa-fw fa-mars"></i>',
			"Female" => '<i class="fas fa-2x fa-fw fa-venus"></i>'];
		foreach ($gender_list as $key => $value) {
			$genders[] = ["text" => $key, "icon" => $value];
		}
		$this->set([
			"ranking" => $ranking,
			"genders" => $genders]);
        return ($this->render("index"));
    }
}
