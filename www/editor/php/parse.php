<?

	require 'vendor/PHPExcel.php';
	
	$dirXl = '../xl';
	$dirCsv = 'csv/';
	$files = array_filter(scandir($dirXl), function($item) {
		return !is_dir($dirXl . $item);
	});
	
	foreach($files as $file){
		$path = $dirXl.'/'.$file;
		$pathCsv = $dirCsv.reset(explode('.',$file)).".csv";
		convertXLStoCSV($path,$pathCsv);
		parseCsv($pathCsv);
	}
	//Usage:
	//convertXLStoCSV('1kursnew.xlsx','output.csv');
	 
	function convertXLStoCSV($infile,$outfile)
	{
		$fileType = PHPExcel_IOFactory::identify($infile);
		$objReader = PHPExcel_IOFactory::createReader($fileType);
	 
		$objReader->setReadDataOnly(true);		
		$objPHPExcel = $objReader->load($infile);    
	 
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'CSV');
		$objWriter->setDelimiter(';');
		$objWriter->setEnclosure('');
        $objWriter->setLineEnding("\r\n");
		$objWriter->save($outfile);
	}
	
	function parseCsv($file_name){
		
		$file_csv = str_replace('"','',file_get_contents($file_name)); 
		$arr_parse = explode("\r\n",$file_csv);
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
			
				if($time && $memmory && $j % 2 === 1 && $j != 0){
					$day = array_search($memmory,$week);
					
					switch($j){
						case 3:
							if(strlen($value)){
								setSubjects($arr_parse,$i,$j,$value,0,$day,$time,$json_arr,$group);
							}
						break;
						case 5:
							if(strlen($value)){
								setSubjects2($arr_parse,$i,$j,$value,1,$day,$time,$json_arr,$group);
							}
						break;
						case 7:
							if(strlen($value)){
								setSubjects($arr_parse,$i,$j,$value,1,$day,$time,$json_arr,$group);
							}
						break;
						case 9:
							if(strlen($value)){
								setSubjects2($arr_parse,$i,$j,$value,2,$day,$time,$json_arr,$group);
							}
						break;
						case 11:
							if(strlen($value)){
								setSubjects($arr_parse,$i,$j,$value,2,$day,$time,$json_arr,$group);
							}
						break;
						case 13:
							if(strlen($value)){
								setSubjects2($arr_parse,$i,$j,$value,3,$day,$time,$json_arr,$group);
							}
						break;
						case 15:
							if(strlen($value)){
								setSubjects($arr_parse,$i,$j,$value,3,$day,$time,$json_arr,$group);
							}
						break;
						case 17:
							if(strlen($value)){
								setSubjects2($arr_parse,$i,$j,$value,4,$day,$time,$json_arr,$group);
							}
						break;
						case 19:
							if(strlen($value)){
								setSubjects($arr_parse,$i,$j,$value,4,$day,$time,$json_arr,$group);
							}
						break;
						case 21:
							if(strlen($value)){
								setSubjects2($arr_parse,$i,$j,$value,5,$day,$time,$json_arr,$group);
							}
						break;
						case 23:
							if(strlen($value)){
								setSubjects($arr_parse,$i,$j,$value,5,$day,$time,$json_arr,$group);
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