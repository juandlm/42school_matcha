<?php

namespace Matcha\Model;

use Matcha\Core\Model;

class UserManager extends Model
{
    public function newUser(User $user)
    {
        $sql = "INSERT INTO t_users (usr_login, usr_pwd, usr_ppic, usr_name, usr_email, usr_social, usr_idsocial, usr_lat, usr_long, usr_city, usr_country, usr_token)
				VALUES (:usr_login, :usr_pwd, :usr_ppic, :usr_name, :usr_email, :usr_social, :usr_idsocial, :usr_lat, :usr_long, :usr_city, :usr_country, :usr_token)";
        $data = $this->db->prepare(
            $sql,
            [
                ':usr_login' => $user->get_usr_login(),
                ':usr_pwd' => $user->get_usr_pwd(),
                ':usr_ppic' => $user->get_usr_ppic(),
                ':usr_name' => $user->get_usr_name(),
                ':usr_email' => $user->get_usr_email(),
                ':usr_social' => $user->get_usr_social(),
                ':usr_idsocial' => $user->get_usr_idsocial(),
                ':usr_lat' => $user->get_usr_lat(),
                ':usr_long' => $user->get_usr_long(),
                ':usr_city' => $user->get_usr_city(),
                ':usr_country' => $user->get_usr_country(),
                ':usr_token' => $user->get_usr_token()
            ],
            false
        );
    }

    public function confirmUser(User $user)
    {
        $sql = "UPDATE t_users
				SET usr_confirmed = 1, usr_token = null
				WHERE usr_login = :usr_login";
        $data = $this->db->prepare(
            $sql,
            [':usr_login' => $user->get_usr_login()],
            false
        );
    }

    public function activateUser(User $user)
    {
        $sql = "UPDATE t_users
				SET usr_active = 1
				WHERE usr_id = :usr_id";
        $data = $this->db->prepare(
            $sql,
            [':usr_id' => $user->get_usr_id()],
            false
        );
    }

    public function deactivateUser(User $user)
    {
        $sql = "UPDATE t_users
				SET usr_active = 0
				WHERE usr_id = :usr_id";
        $data = $this->db->prepare(
            $sql,
            [':usr_id' => $user->get_usr_id()],
            false
        );
    }

    public function existsUser($username, $email)
    {
        $sql = "SELECT *
				FROM t_users
				WHERE usr_login = :usr_login
				OR usr_email = :usr_email";
        $data = $this->db->prepare(
            $sql,
            [
                ':usr_login'    => $username,
                ':usr_email'    => $email
            ],
            true,
            false,
            true
        );
        return ($data);
    }

    public function existsUserSession($user_id)
    {
        $sql = "SELECT ses_id
				FROM t_sessions
				WHERE ses_usr_id = :usr_id";
        $data = $this->db->prepare(
            $sql,
            [':usr_id' => $user_id],
            true,
            false,
            true
        );
        return ($data);
    }

    public function newUserSession($sessionInfo)
    {
        $sql = "INSERT INTO t_sessions (ses_usr_id, ses_token, ses_date, ses_ip)
				VALUES (:ses_usr_id, :ses_token, :ses_date, :ses_ip)";
        $data = $this->db->prepare($sql, $sessionInfo, false);
    }

    public function getUser(User $user)
    {
        $sql = "SELECT *
				FROM t_users
				WHERE usr_id = :usr_id
				OR usr_login = :usr_login
				OR usr_email = :usr_email";
        $data = $this->db->prepare(
            $sql,
            [
                ':usr_id'        => $user->get_usr_id(),
                ':usr_login'    => $user->get_usr_login(),
                ':usr_email'    => $user->get_usr_email()
            ],
            true,
            true,
            false
        );
        return (new \Matcha\Model\User($data));
    }

    public function getUserSocial($id)
    {
        $sql = "SELECT usr_id, usr_login
				FROM t_users
				WHERE usr_idsocial = :id_social";
        $data = $this->db->prepare(
            $sql,
            [':id_social' => $id],
            true,
            true
        );
        return ($data);
    }

    public function checkIfCompleteProfile($usr_id)
    {
        $sql = "SELECT *
				FROM t_users
				WHERE usr_id = :usr_id
				AND usr_name IS NOT NULL
				AND usr_tags IS NOT NULL
				AND usr_dob IS NOT NULL
				AND usr_bio IS NOT NULL
				AND usr_gender IS NOT NULL
				AND usr_orientation IS NOT NULL";
        $data = $this->db->prepare(
            $sql,
            [':usr_id'    => $usr_id],
            true,
            false,
            true
        );
        return ($data);
    }

    public function firstUserPicture(User $user, $img)
    {
        $sql = "UPDATE t_users
				SET usr_ppic = :img
				WHERE usr_id = :usr_id";
        $data = $this->db->prepare(
            $sql,
            [
                ':usr_id'    => $user->get_usr_id(),
                ':img' => $img
            ],
            false
        );
        return ($data);
    }

    public function editUser(User $user)
    {
        $sql = "UPDATE t_users
				SET usr_login = :usr_login, usr_email = :usr_email, usr_pwd = :usr_pwd, usr_token = :usr_token
				WHERE usr_id = :usr_id
				OR usr_login = :usr_login
				OR usr_email = :usr_email";
        $data = $this->db->prepare(
            $sql,
            [
                ':usr_id' => $user->get_usr_id(),
                ':usr_login' => $user->get_usr_login(),
                ':usr_email' => $user->get_usr_email(),
                ':usr_pwd' => $user->get_usr_pwd(),
                ':usr_token' => $user->get_usr_token()
            ],
            false
        );
    }

    public function editProfile(User $user)
    {
        $sql = "UPDATE t_users
				SET usr_name = :usr_name, usr_tags = :usr_tags, usr_dob = :usr_dob,
				usr_bio = :usr_bio, usr_gender = :usr_gender,
				usr_orientation = :usr_orientation
				WHERE usr_id = :usr_id
				AND usr_login = :usr_login";
        $data = $this->db->prepare(
            $sql,
            [
                ':usr_id' => $user->get_usr_id(),
                ':usr_login' => $user->get_usr_login(),
                ':usr_name' => $user->get_usr_name(),
                ':usr_tags' => $user->get_usr_tags(),
                ':usr_dob' => $user->get_usr_dob(),
                ':usr_bio' => $user->get_usr_bio(),
                ':usr_gender' => $user->get_usr_gender(),
                ':usr_orientation' => $user->get_usr_orientation()
            ],
            false
        );
        return ($data);
    }

    public function editPreferences(User $user)
    {
        $sql = "UPDATE t_users
				SET usr_lik_sendmail = :usr_lik_sendmail,
				usr_msg_sendmail = :usr_msg_sendmail,
				usr_vst_sendmail = :usr_vst_sendmail
				WHERE usr_id = :usr_id";
        $data = $this->db->prepare(
            $sql,
            [
                ':usr_id'         => $user->get_usr_id(),
                ':usr_lik_sendmail' => $user->get_usr_lik_sendmail(),
                ':usr_msg_sendmail'    => $user->get_usr_msg_sendmail(),
                ':usr_vst_sendmail'    => $user->get_usr_vst_sendmail()
            ],
            false
        );
    }

    public function editLocation(User $user, $lat, $lng, $city, $country)
    {
        $sql = "UPDATE t_users
				SET usr_lat = :lat, usr_long = :lng, usr_city = :city, usr_country = :country
				WHERE usr_id = :usr_id";
        $data = $this->db->prepare(
            $sql,
            [
                ':usr_id' => $user->get_usr_id(),
                ':lat' => $lat,
                ':lng' => $lng,
                ':city' => $city,
                ':country' => $country
            ],
            false
        );
    }

    public function updateGeolocationConsent(User $user, $geo)
    {
        $sql = "UPDATE t_users
				SET usr_geoconsent = :geo
				WHERE usr_id = :usr_id";
        $data = $this->db->prepare(
            $sql,
            [
                ':usr_id' => $user->get_usr_id(),
                ':geo' => $geo
            ],
            false
        );
    }

    public function fetchUserTags($user_id)
    {
        $sql = "SELECT tag_id, tag_name FROM t_tags A
				INNER JOIN (
							SELECT DISTINCT SUBSTRING_INDEX(SUBSTRING_INDEX(usr_tags, ',', n.digit+1), ',', -1) 'usertags'
				FROM t_users
				INNER JOIN (
							SELECT 0 digit UNION ALL
							SELECT 1 UNION ALL
							SELECT 2 UNION ALL
							SELECT 3 UNION ALL
							SELECT 4 UNION ALL
							SELECT 5 UNION ALL
							SELECT 6 UNION ALL
							SELECT 7 UNION ALL
							SELECT 8 UNION ALL
							SELECT 9 UNION ALL
							SELECT 10 UNION ALL
							SELECT 11 UNION ALL
							SELECT 12 UNION ALL
							SELECT 13 UNION ALL
							SELECT 14 UNION ALL
							SELECT 15 UNION ALL
							SELECT 16 UNION ALL
							SELECT 17 UNION ALL
							SELECT 18 UNION ALL
							SELECT 19 UNION ALL
							SELECT 20
							) n
				ON LENGTH (REPLACE(usr_tags, ',' , '')) <= LENGTH(usr_tags)-n.digit
				WHERE usr_id = :usr_id) B
				ON A.tag_id = B.usertags";
        $data = $this->db->prepare(
            $sql,
            [':usr_id' => $user_id],
            true
        );
        return ($data);
    }

    public function fetchUserRankings()
    {
        $sql = "SELECT usr_login, usr_name, usr_city, LOWER(REPLACE(usr_country, ' ', '-')) usr_flag, usr_country, usr_gender, usr_rating
				FROM t_users
				ORDER BY usr_rating DESC";
        $data = $this->db->query($sql, true);
        return ($data);
    }

    public function updateUserRating($user_id, $points)
    {
        $sql = "UPDATE t_users
				SET usr_rating = usr_rating + :points
				WHERE usr_id = :usr_id";
        $data = $this->db->prepare(
            $sql,
            [
                ':points' => $points,
                ':usr_id' => $user_id
            ],
            false
        );
    }

    public function updateLastSeen(User $user, $last_action)
    {
        $sql = "UPDATE t_users
				SET usr_lastseen = FROM_UNIXTIME(:last_action)
				WHERE usr_id = :usr_id";
        $data = $this->db->prepare(
            $sql,
            [
                ':last_action' => $last_action,
                ':usr_id' => $user->get_usr_id()
            ],
            false
        );
    }

    public function checkBlock($block_from, $block_to)
    {
        $sql = "SELECT *
				FROM t_blocking
				WHERE blo_usr_id_from = :block_from
				AND blo_usr_id_to = :block_to
				AND blo_active = 1";
        $data = $this->db->prepare(
            $sql,
            [
                ':block_from' => $block_from,
                ':block_to' => $block_to
            ],
            true,
            false,
            true
        );
        return ($data);
    }

    public function fetchBlockData($block_from, $block_to)
    {
        $sql = "SELECT *
				FROM t_blocking
				WHERE blo_usr_id_from = :block_from
				AND blo_usr_id_to = :block_to";
        $data = $this->db->prepare(
            $sql,
            [
                ':block_from' => $block_from,
                ':block_to' => $block_to
            ],
            true,
            true
        );
        return ($data);
    }

    public function fetchBlockedUsers($user_id)
    {
        $sql = "SELECT t_blocking.*, t_users.usr_login, t_users.usr_id, t_users.usr_name
				FROM t_blocking, t_users
				WHERE blo_usr_id_from = :usr_id
				AND t_users.usr_id = t_blocking.blo_usr_id_to
				AND blo_active = 1";
        $data = $this->db->prepare(
            $sql,
            [':usr_id' => $user_id],
            true
        );
        return ($data);
    }

    public function removeLike($block_from, $block_to)
    {
        $sql = "UPDATE t_likes
				SET lik_active = 0
				WHERE lik_usr_id_from = :block_from
				AND lik_usr_id_to = :block_to";
        $data = $this->db->prepare(
            $sql,
            [
                ':block_from' => $block_from,
                ':block_to' => $block_to
            ],
            false
        );
    }

    public function toggleBlock($block_from, $block_to, $active)
    {
        $sql = "INSERT INTO t_blocking (blo_usr_id_from, blo_usr_id_to)
				VALUES (:block_from, :block_to)
				ON DUPLICATE KEY UPDATE blo_active = :active";
        $data = $this->db->prepare(
            $sql,
            [
                ':block_from' => $block_from,
                ':block_to' => $block_to,
                ':active' => $active
            ],
            false
        );
    }
}
