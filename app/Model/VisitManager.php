<?php

namespace Matcha\Model;

use Matcha\Core\Model;

class VisitManager extends Model
{
    public function checkRecentVisit($usr_from, $usr_to)
    {
        $sql = "SELECT *
				FROM t_visits
				WHERE vst_usr_id_from = :usr_from
				AND vst_usr_id_to = :usr_to
				AND vst_date > (NOW() - INTERVAL 3 DAY)";
        $data = $this->db->prepare(
            $sql,
            [
                ':usr_from' => $usr_from,
                ':usr_to'     => $usr_to
            ],
            true,
            false,
            true
        );
        return ($data);
    }

    public function fetchVisits($usr_from)
    {
        $sql = "SELECT vst_usr_id_to, usr_id, usr_login, usr_ppic, usr_name, usr_dob, usr_city, usr_lastseen
				FROM t_visits
				INNER JOIN t_users
					ON usr_id = vst_usr_id_to
				WHERE vst_usr_id_from = :usr_id
				AND usr_id
				NOT IN (
						SELECT t_blocking.blo_usr_id_to
						FROM t_blocking
						WHERE t_blocking.blo_usr_id_from = :usr_id
						AND t_blocking.blo_active = 1
						)
				AND usr_id
				NOT IN (
						SELECT t_blocking.blo_usr_id_from
						FROM t_blocking
						WHERE t_blocking.blo_usr_id_to = :usr_id
						AND t_blocking.blo_active = 1
						)
				ORDER BY vst_update DESC
				LIMIT 9";
        $data = $this->db->prepare(
            $sql,
            [':usr_id' => $usr_from],
            true
        );
        return ($data);
    }

    public function newVisit($usr_from, $usr_to)
    {
        $sql = "INSERT INTO t_visits (vst_usr_id_from, vst_usr_id_to, vst_date)
				VALUES (:usr_from, :usr_to, NOW())
				ON DUPLICATE KEY UPDATE vst_date = NOW()";
        $data = $this->db->prepare(
            $sql,
            [
                ':usr_from' => $usr_from,
                ':usr_to' => $usr_to
            ],
            false
        );
        return ($data);
    }

    public function updateLastVisit($usr_from, $usr_to)
    {
        $sql = "INSERT INTO t_visits (vst_usr_id_from, vst_usr_id_to, vst_update, vst_date)
				VALUES (:usr_from, :usr_to, NOW(), (NOW() - INTERVAL 7 DAY))
				ON DUPLICATE KEY UPDATE vst_update = NOW()";
        $data = $this->db->prepare(
            $sql,
            [
                ':usr_from' => $usr_from,
                ':usr_to' => $usr_to
            ],
            false
        );
        return ($data);
    }
}
