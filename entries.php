<?php

include 'header.php';
$insertEntry = new InsertEntry();
$insertEntry->setDB(IoC::resolve('database'));
$media = IoC::resolve("Media");

// if(!$user->is_logged_in()){
//	header('Location: login.php');
// }
?>
	<div id="main">
		<form name="entries" method="get" action="entries.php">
			<div class="tablenav top">
				<div class="alignleft actions">
					<select name="category_id">
						<option>Alle Eintr&auml;ge</option>
						<?php echo $insertEntry->getCategorySelectables(); ?>
					</select>
					<input type="submit" name="" id="doaction" class="button action" value="&Uuml;bernehmen">
					<a href="insert.php">Neuer Eintrag</a>
				</div>
			</div>
			<table class="list-table" cellspacing="0">
				<thead>
					<tr>
						<th class="table-head-checkbox table-head"><input type="checkbox"/></th>				
						<th class="table-head-image table-head">Image</th>			
						<th class="table-head-title table-head">Title</th>
						<th class="table-head-address table-head">Address</th>
					</tr>
				</thead>

				<tbody id="the-list">
					<?php

					$entries = Entry::getEntries((isset($_GET['category_id']) ? $_GET['category_id'] : 0));

					if(is_array($entries)){
						foreach($entries as $entry){							
							$im = IoC::resolve('ImageManipulation');
							$images = $entry->getImages();

							$width = 80;
							$height = 45;
							foreach($images as $image):
								$imageLink = $media->getLink($image->filename, $width, $height); break;
							endforeach;

							echo '
							<tr class="entry">
								<td class="td-checkbox"><input type="checkbox"/></td>
								<td class="td-image"><a href="insert.php?action=edit&id='. $entry->id .'"><img src="'. $imageLink .'" /></a></td>
								<td class="td-title">
									<div class="entry-title">
										<a href="insert.php?action=edit&id='. $entry->id .'">
											'. $entry->title .'
										</a>
									</div>
									<div class="entry-content">
										<div class="entry-description">
											'. substr($entry->description, 0, 255) .'
										</div>
										<div class="actions">
											Actions: <a href="insert.php?action=edit&id='. $entry->id .'">Edit</a> <a href="insert.php?action=delete&id='. $entry->id .'">Delete</a>
										</div>
									</div>
								</td>
								<td class="td-address"></td>
							</tr>';
						}					
					}

					?>
				</tbody>
			</table>
		</form>
	</div>
</body>
</html>