<?php

function recursiveRemove($dir) {
    $structure = glob(rtrim($dir, "/").'/*');
    if (is_array($structure)) {
        foreach($structure as $file) {
            if (is_dir($file)) recursiveRemove($file);
            elseif (is_file($file)) unlink($file);
        }
    }
    rmdir($dir);
}

if(!empty($_FILES)) 
{
	echo $_FILES["fileToUpload"]["name"];
	echo "<br>";
	$uploadOk = 1;
	$target_dir = getcwd()."/tmp/";
	$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
	$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
	if($imageFileType !="zip")
	{
		Header ("Location:index.php?error=1");
		exit();
	}
	if($_FILES["fileToUpload"]["size"] < 1000)
	{
		Header ("Location:index.php?error=2");
		exit();
	}	
    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) 
	{
        
		$zip = new ZipArchive;
		$res = $zip->open($target_file);
		$dir = getcwd();

		if ($res === TRUE)
		{
			$dir = getcwd() . '/config_files/';
			$reports_structure = glob(rtrim($dir, "/").'/*');
			if (is_array($reports_structure)) 
			{
				foreach($reports_structure as $file) 
				{
					if (is_dir($file)) recursiveRemove($file);
					elseif (is_file($file)) unlink($file);
				}
			}
			$zip->extractTo($dir);
			$zip->close();
			unlink($target_file);
			Header ("Location:index.php?error=0");
			exit();
		}
		else 
		{
			Header ("Location:index.php?error=4");
			exit();;
		}
    } 
	else {
        Header ("Location:index.php?error=3");
		exit();
    }

}
else
{
echo "No file upload";
}	

?>