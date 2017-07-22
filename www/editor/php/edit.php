<?
	$group  = $_POST['numberGroup'];
	$groupShedule = $_POST['jsonString'];
	$newSchedule = $_POST['newSchedule'];
	$js = json_encode($groupShedule);
	$php = json_decode(str_replace("'","",$js),true);
	$result = array($group=>"");
	
	foreach($php as $key => $value){
		if(strlen($key) > 0){ 
			$result[$group][$key] = "";
			$result[$group][$key]['date'] = $php[$key]['date'];
			$i = 0;
			foreach($php[$key]['time'] as $va){
				$name = $php[$key]['subjects'][$i];
				$cabinet = $php[$key]['cabinet'][$i];
				$result[$group][$key]['time'][$va] = array("name"=>$name,"cabinet"=>$cabinet);
				$i++;
			}
		}
	}
	$curs = ($newSchedule == 'on') ? $group[0].'n' : $group[0];
	$str = file_get_contents("../../json/".$curs.".json");
	$refresh = json_decode($str,true);
	$refresh[$group] = $result[$group];
	$json_save = json_encode($refresh);
	$file = "../../json/".$curs.".json";
	$size = file_put_contents($file,$json_save);
	if($size) echo "Сохранено";
?>