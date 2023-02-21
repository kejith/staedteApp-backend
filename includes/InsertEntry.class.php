<?php

class InsertEntry {
	private $db;
	private $media;

	public function InsertEntry(){
		$this->media = IoC::resolve("Media");
	}

	public function getCategorySelectables($cID = -1, $depth = 0){
		$str = "";
		// $cID = -1 then output all
		if($cID == -1){
			$this->db->query('SELECT * FROM '. DB::TABLE_CATEGORY .' WHERE category_parent IS NULL;');
		} else {
			$this->db->query('SELECT * FROM '. DB::TABLE_CATEGORY .' WHERE category_parent = "'. $cID .'"');
		}

		if($this->db->count() <= 0)
			return;

		$result = $this->db->getResult();
		while($cat = $result->fetch_object()):
			$str .= '<option value="'. $cat->category_id .'">'. str_repeat("--&nbsp;", $depth) . $cat->category_name .'</option>';
			$str .= $this->getCategorySelectables($cat->category_id, $depth + 1);
		endwhile;

		return $str;
	}

	public function getCategorySelectField($name, $multiple = false, $size = 1){
		$str  = '<select name="'. (string) $name .'" size="'. (string) $size .'" '. (($multiple) ? 'multiple' : '') .'>';
		$str .= $this->getCategorySelectables();
		$str .=	'</select>';

		return $str;
	}

	public function getImageList($entryID = 0){
		// create SQL Statement
		$sql = "SELECT 
					sources_link as link,
					sources_id as id
				FROM 
					". DB::TABLE_SOURCES ." 
				WHERE type = 0
				";

		if($entryID !== 0)
			$sql .= " AND sources_parent = '". $entryID ."'";

		$sql .= ";";

		// execute SQL Statement
		$this->db->query($sql);
		$result = $this->db->getResult();

		if($result == NULL)
			return array();

		$images = array();
		while($image = $result->fetch_object()):
			$images[$image->id] = $image->link;
		endwhile;

		return $images;
	}

	public function addLabelBox(){
	?>
		<div class="add-label small-box">
			<div class="title">Label hinzuf&uuml;gen</div>
			<div class="category_select_field">
				<select name="labels">
					<option value="-1">Label w&auml;hlen</option>
					<?php echo $this->getCategorySelectables(); ?>
				</select>
			</div>
			<div class="submit">
				<input type="submit" value="Hinzuf&uuml;gen"/>
			</div>
		</div>
	<?php
	}

	public function editLabelBox($id = 0){
	?>
		<div class="edit-labels small-box"
		<div class="title">Labels</div>
		<div class="edit-label-container">
			<?php

				$sql = "
					SELECT
						*
					FROM
						". DB::TABLE_LABEL ."
					JOIN ". DB::TABLE_CATEGORY ." ON label_category_id = category_id";

				if($id != 0){
					$sql .= " WHERE label_entry_id = ". $id ."";
					$url_id = "&id=" .$id;
				}

				$this->db->query($sql);
				$result = $this->db->getResult();

				if($result != NULL){
					while($label = $result->fetch_object()){
						echo '<div class="edit-label">
								 <a href="insert.php?action=delete-label&lid='. $label->label_id . $url_id .'"><div class="delete-buton-image"></div></a>
								 <div class="label-name">'. $label->category_name .'</div>
							  </div>';
					}
				}

			?>
		</div>
	<?php
	}

	public function getGallery($entry = null){
		if($entry == null)
			return;

		$imageManipulation = IoC::resolve('ImageManipulation');
		$images = $entry->getImages();

		echo '<div class="insert-entry-gallery box">';
		echo '<div class="title">Gallery</div>';

		foreach($images as $image):
			$imageManipulation->setImagePath("./pictures/". $image->filename);
			$link = $imageManipulation->resizeImage(100, 75, true);
			echo '
				<div class="insert-entry-gallery-item gallery-item box">
					  <a href="insert.php?id='.$entry->ID.'&action=delete-image&iid='. $image->id .'"><div class="delete-buton-image"></div></a>
					  <img src="'. $link .'" title="'. $image->id .'" alt="'. $image->id .'" />
				</div>';
		endforeach;
		echo '</div>';
	}

	public function uploadImageField($entry = null){
		if($entry == null)
			return;

		$idUrl = "id=". $entry->id;
	?>

		<form enctype="multipart/form-data" action="insert.php?<?php echo $idUrl; ?>" method="POST">
			<div class="upload-file box">
				<?php $this->getGallery($entry); ?>
				<input type="file" name="image[]"/>
				<input type="submit" value="Hochladen" />
			</div>
		</form>
	<?php
	}

	public function setDB($db){
		$this->db = $db;
	}

	public function updateRow($table, $columns, $id){
		$this->db->updateRow($table, $columns, "entry_id = ". $id);
	}

	public function createEntry($title, $description, $category_id = 0){
		$sql = "
			INSERT INTO ". DB::TABLE_ENTRY ." 
				  (". EntryTable::FLD_TITLE .", ". EntryTable::FLD_DESCRIPTION .") 
			VALUES('".$title."', '".$description."');";
		
		$result = $this->db->query($sql);

		if($result == false)
			return array("Insert into Database failed");

		$entry_id = $this->db->insert_id;

		if($category_id != 0){
			$sql = "
				INSERT INTO 
					". DB::TABLE_LABEL ." 
				(". LabelTable::FLD_ENTRY_ID .", ". LabelTable::FLD_CATEGORY_ID .") 
				VALUES (". $entry_id .",". $category_id .")";

			$result = $this->db->query($sql);

			if($result == false)
				return array("Insert into Database was successfull. Creation of Lable failed");

		}

		header('Location: insert.php?id='.$entry_id);
	}

	public function addLabel($entry_id, $category_id){
		$sql = "INSERT INTO ". DB::TABLE_LABEL ." (". LabelTable::FLD_CATEGORY_ID .", ". LabelTable::FLD_ENTRY_ID .") VALUES(". $category_id .",". $entry_id .");";
		return $db->query($sql);		
	}

	public function init(){
		global $_POST, $_GET, $files;

		$error = array();

		$title       = $_POST['entry_title'];
		$description = $_POST['entry_description'];
		$label 		 = ((isset($_POST['labels']) && $_POST['labels'] != -1) ? $_POST['labels'] : 0);

		$id = 0;
		if(isset($_GET['id']))
			$id = $_GET['id'];

		$entry = new Entry($id, false);

		if(isset($_GET['action'])){
			switch($_GET['action']){
				case 'delete':
					if(!$entry->delete())
						$error[] = "Entry with ID '". $entry->id ."' could not be deleted.";
					else
						header('Location: entries.php');
				break;

				case 'delete-label':
					$labelID = $_GET['lid'];
					if(!$entry->deleteLabel($labelID))
						$error[] = "Label with ID '". $labelID ."' could not be deleted.";
				break;

				case 'delete-image';
					$imageID = $_GET['iid'];
					if(!$entry->deleteImage($imageID))
						$error[] = "Image with ID '". $imageID ."' could not be deleted.";							
				break;

				case 'create':
					$error[] = $this->createEntry($title, $description, $label);
					print_r($error);
				break;
			}
		}

		if(isset($_POST['labels']) && $_POST['labels'] != -1){
			$entry->addLabel($_POST['labels'], $id);
		}

		if(isset($files['image']) && $id !== 0){
			$this->media->saveUploadedImages($id, $files['image']);
		}

		$columns = array();
		if(isset($_POST['entry_description'])) $columns[EntryTable::FLD_DESCRIPTION] = $_POST['entry_description'];
		if(isset($_POST['entry_title'])) $columns[EntryTable::FLD_TITLE] = $_POST['entry_title'];

		$entry->update($columns);

		$entry->refresh();

		return $entry;
	}
}

?>