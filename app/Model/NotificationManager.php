<?php

namespace Matcha\Model;

use Matcha\Core\Model;

class NotificationManager extends Model
{
    public function newNotification($user_from, $user_to, $notif_type)
    {
        $sql = "INSERT INTO t_notifications (noti_usr_id_from, noti_usr_id_to, noti_type)
				VALUES (:id_from, :id_to, :notif_type)";
        $data = $this->db->prepare(
            $sql,
            [
                ':id_from' => $user_from,
                ':id_to' => $user_to,
                ':notif_type' => $notif_type
            ],
            false
        );
    }

    public function fetchUnseen($usr_id)
    {
        $sql = "SELECT *
				FROM t_notifications
				WHERE noti_usr_id_to = :usr_id
				AND noti_seen = 0";
        $data = $this->db->prepare(
            $sql,
            [':usr_id' => $usr_id],
            true,
            false,
            true
        );
        return ($data);
    }

    public function fetchAllNotifications($usr_id)
    {
        $sql = "SELECT notif.*,
				usr1.usr_login AS 'noti_usr_login_from', usr1.usr_name AS 'noti_usr_name_from',
				usr2.usr_login AS 'noti_usr_login_to', usr2.usr_name AS 'noti_usr_name_to'
				FROM t_users usr1, t_users usr2
					INNER JOIN (
								SELECT *
								FROM t_notifications
								WHERE noti_usr_id_to = :usr_id
								LIMIT 20
								) AS notif
				WHERE usr1.usr_id = notif.noti_usr_id_from
				AND usr2.usr_id = notif.noti_usr_id_to
				AND usr1.usr_id
				NOT IN (
						SELECT t_blocking.blo_usr_id_to
						FROM t_blocking
						WHERE t_blocking.blo_usr_id_from = :usr_id
						AND t_blocking.blo_active = 1
						)
				AND usr1.usr_id
				NOT IN (
						SELECT t_blocking.blo_usr_id_from
						FROM t_blocking
						WHERE t_blocking.blo_usr_id_to = :usr_id
						AND t_blocking.blo_active = 1
						)
				ORDER BY notif.noti_id DESC";
        $data = $this->db->prepare(
            $sql,
            [':usr_id' => $usr_id],
            true
        );
        return ($data);
    }

    public function clearUserNotifications($usr_id)
    {
        $sql = "UPDATE t_notifications
				SET noti_seen = 1
				WHERE noti_usr_id_to = :usr_id";
        $data = $this->db->prepare(
            $sql,
            [':usr_id' => $usr_id],
            false
        );
        return ($data);
    }
}
