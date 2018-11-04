
<html>

<?php 

if (isset($_POST["name"])) {
    $file = 'config_files/suggestions.txt';
    $file_location = 'config_files/';
	
	$t=time();
	rename ($file, $file_location."/suggestions_bak-".$t.".txt");
	file_put_contents($file, $_POST['name']);
 
}else{  
    header('HTTP/1.1 500 No name set');
}



?>


</html>
