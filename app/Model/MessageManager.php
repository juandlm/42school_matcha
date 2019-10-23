<?php
namespace Matcha\Model;

use Matcha\Core\Model;

class MessageManager extends Model
{
	public function fetchUserConversations($user_id) {
		$sql = "SELECT conv.*,
				usr1.usr_login AS 'con_usr_login_1', usr1.usr_name AS 'con_usr_name_1', usr1.usr_ppic AS 'con_usr_ppic_1',
				usr2.usr_login AS 'con_usr_login_2', usr2.usr_name AS 'con_usr_name_2', usr2.usr_ppic AS 'con_usr_ppic_2',
				msg.msg_body, msg.msg_date, msg.msg_seen, msg.msg_usr_id_from
				FROM t_users usr1, t_users usr2
					INNER JOIN (
								SELECT *
								FROM t_conversations 
								WHERE con_usr_id_1 = :usr_id
								OR con_usr_id_2 = :usr_id
								) AS conv
					LEFT JOIN (
								SELECT t_messages.*
								FROM t_messages
								WHERE t_messages.msg_id = (
															SELECT m.msg_id
															FROM t_messages m
															WHERE m.msg_con_id = t_messages.msg_con_id
															ORDER BY m.msg_id DESC
															LIMIT 1
															)
								) AS msg
						ON conv.con_id = msg.msg_con_id
				WHERE usr1.usr_id = conv.con_usr_id_1
				AND usr2.usr_id = conv.con_usr_id_2
				AND conv.con_active = 1
				ORDER BY msg.msg_date DESC";
		$data = $this->db->prepare($sql,
			[':usr_id' => $user_id],
			true);
		return ($data);
	}

	public function newUserConversation($usr_1, $usr_2) {
		$sql = "INSERT INTO t_conversations (con_usr_id_1, con_usr_id_2)
				VALUES (:usr_1, :usr_2)";
		$data = $this->db->prepare($sql,
			[':usr_1' => $usr_1,
			':usr_2' => $usr_2],
			false);
	}

	public function deleteUserConversation($usr_1, $usr_2) {
		$sql = "UPDATE t_conversations
				SET con_active = 0
				WHERE (
						con_active = 1
						AND con_usr_id_1 = :usr_1
						AND con_usr_id_2 = :usr_2
						)
				OR (
					con_active = 1
					AND con_usr_id_1 = :usr_2
					AND con_usr_id_2 = :usr_1
					)";
		$data = $this->db->prepare($sql,
			[':usr_1' => $usr_1,
			':usr_2' => $usr_2],
			false);
		return ($data);
	}

	public function fetchUnseenMessages($usr_id) {
		$sql = "SELECT t_messages.*
				FROM t_messages
				WHERE t_messages.msg_id = (
											SELECT m.msg_id
											FROM t_messages m
											INNER JOIN t_conversations c
												ON c.con_usr_id_1 = :usr_id
												OR c.con_usr_id_2 = :usr_id
											WHERE m.msg_con_id = c.con_id
											AND m.msg_seen = 0
											AND m.msg_usr_id_from != :usr_id
											ORDER BY m.msg_id DESC
											LIMIT 1
											)";
		$data = $this->db->prepare($sql,
			[':usr_id' => $usr_id],
			true, false, true);
		return ($data);
	}

	public function fetchConversationMessages($con_id) {
		$sql = "SELECT msg_usr_id_from, msg_body, msg_date,
				(CASE DATE(msg_date)
					WHEN (subdate(CURRENT_DATE, 1)) THEN 'Yesterday'
					WHEN (CURRENT_DATE) THEN 'Today'
					ELSE DATE_FORMAT(msg_date, '%M %d, %Y')
				END) AS 'msg_when'
				FROM t_messages
				WHERE msg_con_id = :con_id
				ORDER BY msg_date ASC";
		$data = $this->db->prepare($sql,
			[':con_id' => $con_id],
			true);
		return ($data);
	}

	public function newUserMessage(array $message) {
		$sql = "INSERT INTO t_messages (msg_con_id, msg_usr_id_from, msg_body)
					VALUES (:con_id, :sender, :msg_body)";
		$data = $this->db->prepare($sql, 
			[':con_id' => $message["con_id"],
			':sender' => $message["sender"],
			':msg_body' => $message["msg_body"]],
			false);
		return ($data);
	}

	public function clearUnseenUserMessages($con_id, $usr_id) {
		$sql = "UPDATE t_messages
				SET msg_seen = 1
				WHERE msg_con_id = :con_id
				AND msg_usr_id_from != :usr_id";
		$data = $this->db->prepare($sql,
			[':con_id' => $con_id,
			':usr_id' => $usr_id],
			false);
		return ($data);
	}
}