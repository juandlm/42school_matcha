<?php

namespace Matcha\Model;

use Matcha\Core\Model;

class MatchManager extends Model
{
    public function checkIfLikesMatch($usr_1, $usr_2)
    {
        $sql = "SELECT *
				FROM t_likes
				WHERE lik_usr_id_from = :usr_1
				AND lik_usr_id_to = :usr_2
				OR (
					lik_usr_id_from = :usr_2
					AND lik_usr_id_to = :usr_1
					)
				AND lik_active = 1";
        $data = $this->db->prepare(
            $sql,
            [
                ':usr_1' => $usr_1,
                ':usr_2' => $usr_2
            ],
            true,
            false,
            true
        );
        return ($data);
    }

    public function checkMatch($usr_1, $usr_2)
    {
        $sql = "SELECT *
				FROM t_matches
				WHERE (
						mat_active = 1
						AND mat_usr_id_1 = :usr_1
						AND mat_usr_id_2 = :usr_2
						)
				OR (
					mat_active = 1
					AND mat_usr_id_1 = :usr_2
					AND mat_usr_id_2 = :usr_1
					)";
        $data = $this->db->prepare(
            $sql,
            [
                ':usr_1' => $usr_1,
                ':usr_2' => $usr_2
            ],
            true,
            false,
            true
        );
        return ($data);
    }

    public function fetchMatchData($usr_1, $usr_2)
    {
        $sql = "SELECT *
				FROM t_matches
				WHERE (
						mat_active = 1
						AND mat_usr_id_1 = :usr_1
						AND mat_usr_id_2 = :usr_2
						)
				OR (
					mat_active = 1
					AND mat_usr_id_1 = :usr_2
					AND mat_usr_id_2 = :usr_1
					)";
        $data = $this->db->prepare(
            $sql,
            [
                ':usr_1' => $usr_1,
                ':usr_2' => $usr_2
            ],
            true,
            true
        );
        return ($data);
    }

    public function fetchUserMatches($usr_id)
    {
        // $sql = "SELECT mat.*,
        // 		usr.usr_login AS 'mat_login',
        // 		usr.usr_ppic AS 'mat_ppic',
        // 		usr.usr_name AS 'mat_name'
        // 		FROM t_matches mat
        // 		INNER JOIN (
        // 					SELECT t_users.*
        // 					FROM t_users, t_matches
        // 					WHERE t_users.usr_id != :usr_id
        // 					AND (t_users.usr_id = t_matches.mat_usr_id_1
        // 					OR t_users.usr_id = t_matches.mat_usr_id_2)
        // 					) AS usr
        // 		WHERE mat.mat_active = 1
        // 		AND (mat.mat_usr_id_1 = :usr_id
        // 		OR mat.mat_usr_id_2 = :usr_id)";
        $sql = "SELECT mat.*,
				usr.usr_login AS 'mat_login',
				usr.usr_ppic AS 'mat_ppic',
				usr.usr_name AS 'mat_name'
				FROM t_matches mat, t_users usr
				WHERE mat.mat_active = 1
				AND usr.usr_id != :usr_id
				AND (mat.mat_usr_id_1 = :usr_id
				OR mat.mat_usr_id_2 = :usr_id)
				AND (usr.usr_id = mat.mat_usr_id_1
				OR usr.usr_id = mat.mat_usr_id_2)";
        $data = $this->db->prepare(
            $sql,
            [':usr_id' => $usr_id],
            true
        );
        return ($data);
    }

    public function newMatch($usr_1, $usr_2)
    {
        $sql = "INSERT INTO t_matches (mat_usr_id_1, mat_usr_id_2)
				VALUES (:usr_1, :usr_2)
				ON DUPLICATE KEY UPDATE mat_active = 1";
        $data = $this->db->prepare(
            $sql,
            [
                ':usr_1' => $usr_1,
                ':usr_2' => $usr_2
            ],
            false
        );
        return ($data);
    }

    public function deleteMatch($usr_1, $usr_2)
    {
        $sql = "UPDATE t_matches
				SET mat_active = 0
				WHERE (
						mat_active = 1
						AND mat_usr_id_1 = :usr_1
						AND mat_usr_id_2 = :usr_2
						)
				OR (
					mat_active = 1
					AND mat_usr_id_1 = :usr_2
					AND mat_usr_id_2 = :usr_1
					)";
        $data = $this->db->prepare(
            $sql,
            [
                ':usr_1' => $usr_1,
                ':usr_2' => $usr_2
            ],
            false
        );
        return ($data);
    }

    public function checkUnmatch($usr_1, $usr_2)
    {
        $sql = "SELECT *
				FROM t_matches
				WHERE (
						mat_active = 0
						AND mat_usr_id_1 = :usr_1
						AND mat_usr_id_2 = :usr_2
						)
				OR (
					mat_active = 0
					AND mat_usr_id_1 = :usr_2
					AND mat_usr_id_2 = :usr_1
					)";
        $data = $this->db->prepare(
            $sql,
            [
                ':usr_1' => $usr_1,
                ':usr_2' => $usr_2
            ],
            true,
            false,
            true
        );
        return ($data);
    }
}
