<?
	$dir = '../json/';
	$files = scandir($dir);
	$new_name = "";
	
	foreach($files as $value){
		$file = $dir.''.$value;
		$pathinfo = pathinfo($file);
		
		if($pathinfo['extension'] === 'json'){
			echo $pathinfo['filename']."<br>";
			if(strlen($pathinfo['filename']) === 1){
				$new_name = $pathinfo['basename'];
				print_r(unlink($file));
			}else{
				print_r(rename($file,$dir.''.$new_name));
			}
		}
	}
?>	