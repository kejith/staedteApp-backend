<?php

include 'header.php';

$insertEntry = new InsertEntry();
$insertEntry->setDB(IoC::resolve('database'));

$entry = $insertEntry->init();

?>
<?php if($entry->id > 0): ?>
	<div id="main">
		<form method="post" action="insert.php?id=<?php echo $entry->id; ?>">
			<div id="header" class="box">
				<div class="title">Firmeneintrag erstellen</div>
			</div>
			<div id="sidebar" class="box">
				<div class="sidebar-box">
					<?php $insertEntry->addLabelBox(); ?>
				</div>

				<div class="sidebar-box">
					<?php $insertEntry->editLabelBox($entry->id); ?>
				</div>
			</div>	
			<div id="leftContent">
				<div id="title" class="box">
					<div class="title_input_field">
						<input type="text" name="entry_title" value="<?php echo $entry->title; ?>" />
					</div>
				</div>
				<div id="content" class="box">
					<textarea class="ckeditor" id="editor1" name="entry_description" rows="20"><?php echo $entry->description; ?></textarea>
				</div>
			</div>
			<div class="sidebar-box">
				<?php 
					$addresses = $entry->getAddresses();
					if(!empty($addresses)){
						foreach($addresses as $a){
							?>
							<fieldset class="admin-addresses">
								<a href="insert.php?id=<?php echo $entry->id; ?>&action=delete-address&aid=<?php echo $a->id; ?>"><div class="delete-buton-image"></div></a>
								<legend>Address[ID: <?php echo $a->id; ?>]</legend>
								<ul>
									<li><span>Filiale</span><input name="<?php echo AddressesTable::FLD_TITLE;?>[<?php echo $a->id;?>]"type="text" value="<?php echo $a->id;?>"/></li>
									<li><span>Stra&szlig;e</span><input name="<?php echo AddressesTable::FLD_STREET;?>[<?php echo $a->id;?>]"type="text" value="<?php echo $a->street;?>"/></li>
									<li><span>H-Nr</span><input name="<?php echo AddressesTable::FLD_STREET_NUMBER;?>[<?php echo $a->id;?>]"type="text" value="<?php echo $a->street_number;?>"/></li>
									<li><span>PLZ</span><input name="<?php echo AddressesTable::FLD_ZIPCODE;?>[<?php echo $a->id;?>]"type="text" value="<?php echo $a->zipcode;?>"/></li>
									<li><span>ORT</span><input name="<?php echo AddressesTable::FLD_CITY;?>[<?php echo $a->id;?>]"type="text" value="<?php echo $a->city;?>"/></li>
									<li><span>Land</span><input name="<?php echo AddressesTable::FLD_COUNTRY;?>[<?php echo $a->id;?>]"type="text" value="<?php echo $a->country;?>"/></li>
								</ul>
							</fieldset>
							<?php
						}
					}?>
						<fieldset class="admin-addresses">
							<legend>Addresse hinzufuegen</legend>
							<ul>
								<li><span>Filiale</span><input name="<?php echo AddressesTable::FLD_TITLE;?>[1000]"type="text" value="<?php echo $a->id;?>"/></li>
								<li><span>Stra&szlig;e</span><input name="<?php echo AddressesTable::FLD_STREET;?>[1000]"type="text" value="<?php echo $a->street;?>"/></li>
								<li><span>H-Nr</span><input name="<?php echo AddressesTable::FLD_STREET_NUMBER;?>[1000]"type="text" value="<?php echo $a->street_number;?>"/></li>
								<li><span>PLZ</span><input name="<?php echo AddressesTable::FLD_ZIPCODE;?>[1000]"type="text" value="<?php echo $a->zipcode;?>"/></li>
								<li><span>ORT</span><input name="<?php echo AddressesTable::FLD_CITY;?>[1000]"type="text" value="<?php echo $a->city;?>"/></li>
								<li><span>Land</span><input name="<?php echo AddressesTable::FLD_COUNTRY;?>[1000]"type="text" value="<?php echo $a->country;?>"/></li>
							</ul>
						</fieldset>
						<?php
				?>
			</div>
		</form>
		<?php $insertEntry->uploadImageField($entry); ?>
	</div>
<?php else: ?>
	<form method="post" action="insert.php?action=create">
		<div id="main">
			<div id="header" class="box">
				<div class="title">Firmeneintrag erstellen</div>
			</div>
			<div id="sidebar" class="box">
				<div class="sidebar-box">
					<?php $insertEntry->addLabelBox(); ?>
				</div>
			</div>	
			<div id="leftContent">
				<div id="title" class="box">
					<div class="title_input_field">
						<input type="text" name="entry_title" />
					</div>
				</div>
				<div id="content" class="box">
					<textarea class="ckeditor" id="editor1" name="entry_description" rows="20"></textarea>
				</div>
				<?php $insertEntry->uploadImageField(null); ?>
			</div>

			<pre>
				<?php print_r($_POST); ?>
			</pre>

		</div>
	</form>
<?php endif; ?>
</div>
</body>
</html>