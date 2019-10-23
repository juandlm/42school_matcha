<?php
namespace Matcha\Model;

use Matcha\Core\Model;

class LikeManager extends Model
{
	public function checkLike($usr_from, $usr_to) {
		$sql = "SELECT *
				FROM t_likes
				WHERE lik_usr_id_from = :usr_from
				AND lik_usr_id_to = :usr_to
				AND lik_active = 1";
		$data = $this->db->prepare($sql,
			[':usr_from' => $usr_from,
			':usr_to'     => $usr_to],
		true, false, true);
		return ($data);
	}

	public function checkRecentLike($usr_from, $usr_to) {
		$sql = "SELECT *
				FROM t_likes
				WHERE lik_usr_id_from = :usr_from
				AND lik_usr_id_to = :usr_to
				AND lik_date > (NOW() - INTERVAL 3 DAY)
				AND lik_active = 1";
		$data = $this->db->prepare($sql,
			[':usr_from' => $usr_from,
			':usr_to'     => $usr_to],
		true, false, true);
		return ($data);
	}

	public function fetchLikeData($usr_from, $usr_to) {
		$sql = "SELECT *
				FROM t_likes
				WHERE lik_usr_id_from = :usr_from
				AND lik_usr_id_to = :usr_to";
		$data = $this->db->prepare($sql,
			[':usr_from' => $usr_from,
			':usr_to'     => $usr_to],
		true, true);
		return ($data);
	}

	public function newLike($usr_from, $usr_to) {
		$sql = "INSERT INTO t_likes (lik_usr_id_from, lik_usr_id_to, lik_date)
				VALUES (:usr_from, :usr_to, NOW())
				ON DUPLICATE KEY UPDATE lik_date = NOW(), lik_active = 1";
		$data = $this->db->prepare($sql,
			[':usr_from' => $usr_from,
			':usr_to' => $usr_to],
			false);
		return ($data);
	}
}