<?php

include 'common.php';

if(isset($_GET['api'])):
	try {
		switch($_GET['api']){
		    case 'entry':
		        $entrieXML = IoC::resolve('GetEntryApi');
		        $entrieXML->loadXML();
		        break;
		    case 'category':
	       		$categoryXML = IoC::resolve('GetCategoryApi');
	        	$categoryXML->loadXML();
		        break;
		    case 'im':
		    	$image = 'images/' . $_GET['image'];
		    	if(file_exists($image)){
		    		$imageManipulation = new ImageManipulation($image);
		    		echo '';
		    	} else {
			        echo '
	                <html>
	                    <body>
	                        Das angegebene Bild kann nicht gefunden werden
	                    </body>
	                </html>';		    		
		    	}
		    	break;
		    case 'labels':
		    	$labelsXML = IoC::resolve('GetLabelsApi');
		    	$labelsXML->loadXML();
		    break;
		    case 'default':
		        echo '
                <html>
                    <body>
                        Bitte geben Sie einen gueltigen API-Parameter an</br>
                        Actions</br>
                        entry</br>
                        category</br>
                    </body>
                </html>';
		        break;
		}
	} catch (customException $e) {
    	echo $e->errorMessage();
    }
else:
     echo '
 		<html>
 			<body>
 				Bitte geben Sie den Get-Parameter ACTION an<br>
 				<br>
 				Navigation:<br>
 				<a href="insert.php">Firmen eintragen</a>
 			</body>
 		</html>';
 		phpinfo();
endif;
?>
