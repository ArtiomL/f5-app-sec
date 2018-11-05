
<html>

<?php 

if (isset($_POST["name"]) && isset($_POST["policy"])) {
    $file = 'config_files/'.$_POST["policy"]."/suggestions.txt";
    $file_location = 'config_files/'.$_POST["policy"];
	
	$t=time();
	rename ($file, $file_location."/suggestions_bak-".$t.".txt");
	file_put_contents($file, $_POST['name']);
 
}else{  
    header('HTTP/1.1 500 No policy/variable set');
}



?>


</html>
