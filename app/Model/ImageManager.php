<?php
namespace Matcha\Model;

use Matcha\Core\Model;

class ImageManager extends Model
{
	public function newUpload($usr_id, $upl_path) {
		$sql = "INSERT INTO t_uploads (upl_usr_id, upl_url)
				VALUES (:usr_id, :upl_path)";
		$data = $this->db->prepare($sql,
			[':usr_id' => $usr_id,
			':upl_path' => $upl_path],
			false);
		return ($data);
	}

	public function addUserPicture($usr_id, $picn, $img_name) {
		$sql = "INSERT INTO t_pictures (pic_usr_id, {$picn})
				VALUES (:usr_id, :img_name)
				ON DUPLICATE KEY UPDATE {$picn} = :img_name";
		$data = $this->db->prepare($sql,
			[':usr_id' => $usr_id,
			':img_name' => $img_name],
			false);
		return ($data);
	}

	public function fetchUserPictures(User $user) {
		$sql = "SELECT pic_1 AS '1', pic_2 as '2', pic_3 as '3', pic_4 as '4'
				FROM t_pictures
				WHERE pic_usr_id = :usr_id";
		$data = $this->db->prepare($sql,
			[':usr_id' => $user->get_usr_id()],
			true, true);
		return ($data);
	}

	public function updateProfilePicture(User $user, $picn) {
		$sql = "UPDATE t_pictures p1, t_pictures p2, t_users u
				SET p1.{$picn} = u.usr_ppic, 
				u.usr_ppic = p2.{$picn}
				WHERE p1.pic_id = p2.pic_id
				AND p1.pic_usr_id = :usr_id
				AND p2.pic_usr_id = :usr_id
				AND u.usr_id = :usr_id";
		$data = $this->db->prepare($sql,
			[':usr_id' => $user->get_usr_id()],
			false);
		return ($data);
	}

	public function deletePicture(User $user, $picn) {
		$sql = "UPDATE t_pictures
				SET {$picn} = NULL
				WHERE pic_usr_id = :usr_id";
		$data = $this->db->prepare($sql,
			[':usr_id' => $user->get_usr_id()],
			false);
		return ($data);
	}
}