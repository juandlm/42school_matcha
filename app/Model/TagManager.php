<?php
namespace Matcha\Model;

use Matcha\Core\Model;

class TagManager extends Model
{
	public function searchTags($tag_query) {
		$sql = "SELECT tag_id, tag_name
				FROM t_tags
				WHERE tag_name LIKE ?";
		$data = $this->db->prepare($sql,
			[$tag_query],
			true);
		return ($data);
	}

	public function newTag($user_id, $tag_name) {
		$sql = "INSERT INTO t_tags (tag_creator, tag_name)
				VALUES (:usr_id, :tag_name);";
		$data = $this->db->prepare($sql,
			[":usr_id" => $user_id,
			":tag_name" => $tag_name],
			false);
		$id = $this->db->query("SELECT LAST_INSERT_ID() tag_id", true, true);
		return ($id);
	}
}