<?
	
	// В PHP 4.1.0 и более ранних версиях следует использовать $HTTP_POST_FILES
	// вместо $_FILES.

	$uploaddir = '../csv/';
	$uploadfile = $uploaddir . basename($_FILES['userfile']['name']);
	echo '<pre>';
	if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {
		echo "Файл корректен и был успешно загружен.\n";
	
		$file_name = $uploadfile;
		$file_csv = iconv("windows-1251","utf-8",file_get_contents($file_name));
		$arr_parse = explode("\n",$file_csv);
		for($i = 0; $i < count($arr_parse); $i++){
			$arr_parse[$i] = explode(";",$arr_parse[$i]);
		}
	
		$json_arr;
		$memmory = "";
		$time = "";
		$group;
		$cabinet = "";
		$number = 0;
	
		$week = array(
			"Monday"=>"ПОНЕДЕЛЬНИК",
			"Tuesday"=>"ВТОРНИК",
			"Wednesday"=>"СРЕДА",
			"Thursday"=>"ЧЕТВЕРГ",
			"Friday"=>"ПЯТНИЦА",
			"Saturday"=>"СУББОТА");
	
		for($i = 0; $i < count($arr_parse);$i++){
			for($j = 0; $j < count($arr_parse[$i]);$j++){
				$value = $arr_parse[$i][$j];
				if($memmory == "Группа" && $value > 0 && strlen($value) == 5){
					$value = str_replace(23,"",$value);
					$group[] = $value;
					$json_arr[$value] = "";
				}
			
				if($time && $memmory && $j % 2 === 0 && $j != 0){
					$day = array_search($memmory,$week);
					
					switch($j){
						case 2:
							if(strlen($value)){
								setSubjects($arr_parse,$i,$j,$value,0,$day,$time,$json_arr,$group);
							}
						break;
						case 4:
							if(strlen($value)){
								setSubjects2($arr_parse,$i,$j,$value,0,$day,$time,$json_arr,$group);
							}
						break;
						case 6:
							if(strlen($value)){
								setSubjects($arr_parse,$i,$j,$value,1,$day,$time,$json_arr,$group);
							}
						break;
						case 8:
							if(strlen($value)){
								setSubjects2($arr_parse,$i,$j,$value,1,$day,$time,$json_arr,$group);
							}
						break;
						case 10:
							if(strlen($value)){
								setSubjects($arr_parse,$i,$j,$value,2,$day,$time,$json_arr,$group);
							}
						break;
						case 12:
							if(strlen($value)){
								setSubjects2($arr_parse,$i,$j,$value,2,$day,$time,$json_arr,$group);
							}
						break;
						case 14:
							if(strlen($value)){
								setSubjects($arr_parse,$i,$j,$value,3,$day,$time,$json_arr,$group);
							}
						break;
						case 16:
							if(strlen($value)){
								setSubjects2($arr_parse,$i,$j,$value,3,$day,$time,$json_arr,$group);
							}
						break;
						case 18:
							if(strlen($value)){
								setSubjects($arr_parse,$i,$j,$value,4,$day,$time,$json_arr,$group);
							}
						break;
						case 20:
							if(strlen($value)){
								setSubjects2($arr_parse,$i,$j,$value,4,$day,$time,$json_arr,$group);
							}
						break;
					}
				}
				
				if(trim($value) == "Группа") $memmory = trim($value);
				else if(strpos($value,$week["Monday"]) !== false){
					setWeekDay($value,'Monday',$week,$memmory,$json_arr);
				} else if(strpos($value,$week["Tuesday"]) !== false){
					setWeekDay($value,'Tuesday',$week,$memmory,$json_arr);
				}else if(strpos($value,$week["Wednesday"]) !== false){
					setWeekDay($value,'Wednesday',$week,$memmory,$json_arr);
				}else if(strpos($value,$week["Thursday"]) !== false){
					setWeekDay($value,'Thursday',$week,$memmory,$json_arr);
				}else if(strpos($value,$week["Friday"]) !== false){
					setWeekDay($value,'Friday',$week,$memmory,$json_arr);
				}else if(strpos($value,$week["Saturday"]) !== false){
					setWeekDay($value,'Saturday',$week,$memmory,$json_arr);
				}else if(preg_match("/[0-9]{1,2}:[0-9]{1,2}/",$value,$regs)){
					$time = trim($value);
					if(strpos($time,' ') != false || strlen($time) == 1){
						$time = false;
						continue;
					}
					$day = array_search($memmory,$week);
					$json_arr = setTime($day,$time,$json_arr);
				}
			}
		}
		$json = json_encode($json_arr);
		$file_name = end(explode('/',$file_name));
		$path = "../../json/".$file_name[0].".json";
		if(file_exists($path)) $path = "../../json/".$file_name[0]."n.json";
		$fpc = file_put_contents($path,$json);
		var_dump($fpc);
		echo "<a href='../parse.html'>Вернуться</a> ";
		echo "<a href='../index.html'>Главная</a>";
	} else {
		echo "Возможная атака с помощью файловой загрузки!\n";
	}
	
	function setWeekDay($value,$weekDay,$week,&$memmory,&$json_arr){
			$memmory = $week[$weekDay];
			$date = end(explode(' ',trim($value)));
			$json_arr = setDay($weekDay,$date,$json_arr);
	}
	
	function setSubjects2($arr_parse,$i,$j,$value,$number,$day,$time,&$json_arr,$group){
		$cabinet1 = $arr_parse[$i][$j+1];
		$n = $group[$number];
		$name = ($json_arr[$n][$day]['time'][$time]["name"]) ? 
		$json_arr[$n][$day]['time'][$time]["name"]."/".$value."(2 группа)" :
			$value."(2 группа)";
		$cabinet = ($json_arr[$n][$day]['time'][$time]["cabinet"] && $cabinet1) ?
		$json_arr[$n][$day]['time'][$time]["cabinet"]."/".$cabinet1 :
			$cabinet1;
		$json_arr[$n][$day]['time'][$time]["name"] = ($name) ? $name : "";
		$json_arr[$n][$day]['time'][$time]["cabinet"] = ($cabinet) ? $cabinet : "";
	}
	
	function setSubjects($arr_parse,$i,$j,$value,$number,$day,$time,&$json_arr,$group){
		$cabinet = "";
		$n = $group[$number];
		$cabinet1 = $arr_parse[$i][$j+1];
		$cabinet2 = $arr_parse[$i][$j+3];
		$cabinet3 = $arr_parse[$i][$j+7];
		if(strlen($cabinet1) > 0){
			$value = $value."(1 группа)";
			$cabinet = $cabinet1;
		}elseif(strlen($cabinet2) > 0){
			$cabinet = $cabinet2;
		}elseif(strlen($cabinet3) > 0){
			$cabinet = $cabinet3;
			$n3 = $group[$number+1];
			$json_arr[$n3][$day]['time'][$time] = array(
				"name"=> ($value) ? $value : "",
				"cabinet"=>($cabinet) ? $cabinet : ""
			);			
		}
			
		$json_arr[$n][$day]['time'][$time] = array(
			"name"=>($value) ? $value : "",
			"cabinet"=>($cabinet) ? $cabinet : ""
		);
	}
		
	function setTime($day,$time,$arr){
		foreach($arr as $key=>$value){
			$arr[$key][$day]['time'][$time] = "";
		}
		return $arr;
	}
		
	function setDay($day,$date,$arr){
		foreach($arr as $key=>$value){
			$arr[$key][$day] = "";
			$arr[$key][$day]['date'] = $date;
		}
		return $arr;
	}
?>