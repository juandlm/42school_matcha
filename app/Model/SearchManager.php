<?php

namespace Matcha\Model;

use Matcha\Core\Model;

class SearchManager extends Model
{
    public function fetchMatchSuggestions(User $user, array $filter_params = null, string $query = null)
    {
        $distance = "ATAN2(SQRT(POW(COS(RADIANS(t_users.usr_lat)) * SIN(RADIANS(:usr_long - t_users.usr_long)), 2) + POW(COS(RADIANS(:usr_lat)) * SIN(RADIANS(t_users.usr_lat)) - SIN(RADIANS(:usr_lat)) * COS(RADIANS(t_users.usr_lat)) * COS(RADIANS(:usr_long - t_users.usr_long)), 2)), (SIN(RADIANS(:usr_lat)) * SIN(RADIANS(t_users.usr_lat)) + COS(RADIANS(:usr_lat)) * COS(RADIANS(t_users.usr_lat)) * COS(RADIANS(:usr_long - t_users.usr_long)))) * 6372.795";
        $age = "((YEAR(CURRENT_DATE) - YEAR(t_users.usr_dob)) - (DATE_FORMAT(CURRENT_DATE, '%m%d') < DATE_FORMAT(t_users.usr_dob, '%m%d')))";
        if (($user->get_usr_gender() ^ $user->get_usr_orientation()) === 1) {
            $attraction = "t_users.usr_gender = ABS(:usr_gender - 1)
							AND t_users.usr_orientation != t_users.usr_gender";
        } else if (($user->get_usr_gender() ^ $user->get_usr_orientation()) === 0) {
            $attraction = "t_users.usr_gender = :usr_gender
							AND t_users.usr_orientation != ABS(t_users.usr_gender - 1)";
        } else {
            $attraction = "(t_users.usr_orientation = :usr_orientation
								OR (
									t_users.usr_gender = ABS(:usr_gender - 1)
									OR t_users.usr_gender = :usr_gender
									AND t_users.usr_orientation = :usr_gender
									)
								)";
        }
        $ratingRange = [
            "lower" => (isset($filter_params["rating"][0]) && $filter_params["rating"][0] == 1000) ? 0 :
                "(
			SELECT MIN(low)
			FROM (
				SELECT usr_rating low
				FROM t_users
				WHERE usr_rating <= (SELECT usr_rating FROM t_users WHERE usr_id = :usr_id)
				ORDER BY usr_rating DESC
				LIMIT 0, " . ($filter_params["rating"][0] ?? 200) . "
				) AS rangeA
			)",
            "upper" => (isset($filter_params["rating"][1]) && $filter_params["rating"][1] == 1000) ? 2147483647 :
                "(
			SELECT MAX(high)
			FROM (
				SELECT usr_rating high
				FROM t_users
				WHERE usr_rating >= (SELECT usr_rating FROM t_users WHERE usr_id = :usr_id)
				ORDER BY usr_rating ASC
				LIMIT 0, " . ($filter_params["rating"][1] ?? 200) . "
				) AS rangeB
			)"
        ];
        if (!empty($query)) {
            $query = '%' . $query . '%';
        }

        $sql = "SELECT t_users.usr_login, t_users.usr_id, t_users.usr_name, t_users.usr_tags, t_users.usr_gender, t_users.usr_orientation,
				t_users.usr_dob, t_users.usr_ppic, t_users.usr_city, t_users.usr_rating, t_users.usr_lastseen,
				{$age} AS 'usr_age',
				{$distance} AS 'usr_dist'
				FROM t_users
				WHERE t_users.usr_id 
				NOT IN (
						SELECT t_matches.mat_usr_id_1
						FROM t_matches
						WHERE t_matches.mat_usr_id_2 = :usr_id
						AND t_matches.mat_active = 1
						)
				AND t_users.usr_id 
				NOT IN (
						SELECT t_matches.mat_usr_id_2
						FROM t_matches
						WHERE t_matches.mat_usr_id_1 = :usr_id
						AND t_matches.mat_active = 1
						)
				AND t_users.usr_id 
				NOT IN (
						SELECT t_blocking.blo_usr_id_to
						FROM t_blocking
						WHERE t_blocking.blo_usr_id_from = :usr_id
						AND t_blocking.blo_active = 1
						)
				AND t_users.usr_id 
				NOT IN (
						SELECT t_blocking.blo_usr_id_from
						FROM t_blocking
						WHERE t_blocking.blo_usr_id_to = :usr_id
						AND t_blocking.blo_active = 1
						)
				AND t_users.usr_id != :usr_id
				" . (!empty($query) ? "AND (t_users.usr_login LIKE :query
					OR t_users.usr_name LIKE :query
					OR t_users.usr_city LIKE :query
					OR t_users.usr_country LIKE :query)" : '') . "
				AND {$attraction}
				AND ({$age} BETWEEN " . ($filter_params['age'][0] ?? 0) . " AND " . ($filter_params['age'][1] ?? 99) . ")
				AND {$distance} BETWEEN 0 AND " . ($filter_params["distance"] ?? 500) . "
				AND t_users.usr_rating BETWEEN {$ratingRange["lower"]} AND {$ratingRange["upper"]}";

        $data = $this->db->prepare(
            $sql,
            [
                ":usr_id" => $user->get_usr_id(),
                ":usr_lat" => $user->get_usr_lat(),
                ":usr_long" => $user->get_usr_long(),
                ":usr_gender" => $user->get_usr_gender(),
                ":usr_orientation" => $user->get_usr_orientation(),
                ":query" => $query
            ],
            true
        );
        return ($data);
    }
}
