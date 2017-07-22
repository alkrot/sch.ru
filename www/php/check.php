<?
	$curs = $_GET['curs'];
	$length = strlen($curs);
	$new = strpos($curs,'n');
	$file = '../json/'.$curs.'.json';
	$error = '{"error":"\u0422\u0430\u043a\u043e\u0433\u043e \u043a\u0443\u0440\u0441\u0430 \u043d\u0435\u0442"}';
	
	if(file_exists($file))
		echo file_get_contents($file);
	else if($new > 0 &&  $length === 2)
		echo '{"error":"\u041d\u043e\u0432\u043e\u0433\u043e \u0440\u0430\u0441\u043f\u0438\u0441\u0430\u043d\u0438\u044f \u0435\u0449\u0435 \u043d\u0435\u0442"}';
	else echo $error;
?>