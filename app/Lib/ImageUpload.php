<?php
namespace Matcha\Lib;

class ImageUpload
{
	public $img_url;
	public $img_path;
	public $img_name;
	public $img_type;

	public function ajaxUpload($usr_id) {
		try {
			if (empty($_FILES["image"]))
				throw new \Exception("No file was selected.");

			$image = $_FILES["image"];
			$this->img_type = $image["type"];

			if ($image["error"] !== 0) {
				if ($image["error"] === 1)
					throw new \Exception("Max upload size exceeded.");	
				throw new \Exception("Image uploading error: Initialization error.");
			}

			if (!file_exists($image["tmp_name"]))
				throw new \Exception("Image file is missing in the server.");
		
			$maxFileSize = 2 * 10e5; // in bytes
			if ($image["size"] > $maxFileSize)
				throw new \Exception("Size limit exceeded.");	
		
			$imageData = getimagesize($image["tmp_name"]);
			if (!$imageData) 
				throw new \Exception("Invalid file.");
		
			$mimeType = $imageData["mime"];
			$allowedMimeTypes = ["image/jpeg", "image/png", "image/gif"];
			if (!in_array($mimeType, $allowedMimeTypes)) 
				throw new \Exception("Only .jpeg and .png files are allowed.");
		
			$fileExtension = strtolower(pathinfo($image["name"], PATHINFO_EXTENSION));
			$fileName = round(microtime(true)) . mt_rand() . "." . $fileExtension;
			$this->img_name = $fileName;
			$this->img_path = "assets/userphotos/" . $fileName;
			$destination = ROOT . $this->img_path;
			$moveFile = move_uploaded_file($image["tmp_name"], $destination);
			if ($moveFile) {
				$this->img_url = URL . $this->img_path;
				$imageManager = new \Matcha\Model\ImageManager();
				if ($imageManager->newUpload($usr_id, $this->img_path)) {
					return (json_encode(
						["status" => true,
						"url" => $this->img_url]
					));
				} else 
					throw new \Exception("Could not insert data into the database.");
			} else 
				throw new \Exception("The image could not be moved to the destination folder.");
		} catch (\Exception $e) {
			exit (json_encode(
				["status" => false,
				"error" => $e -> getMessage()]
			));
		}
	}
}