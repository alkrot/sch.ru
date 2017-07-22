<?
$curs = 4;
$file = '../json/'.$curs.'.json';
$res = json_decode(file_get_contents($file),true);
$test = count($res);
$m = array("Организация работы серверных операционных систем","Сетевые технологии");

$new = array();

foreach($m as $val){
	$new += getSchedue($res,$val);
}

var_dump($new);

function getSchedue($res,$search){
	$keys = array_keys($res);
	$output = array();
	foreach($keys as $key){
		$group = $res[$key];
		foreach($group as $k=>$value){
			$schedule = $group[$k]['time'];
			foreach($schedule as $time=> $val){
				/* echo "Время: {$time} Группа: {$key} Предмет: {$val['name']} Кабинет: {$val['cabinet']}<br>"; */
				if(strpos($val['name'],$search) !== false){
					$output[$search][$k] = array("group"=>$key,"cabinet"=>$val['cabinet'],"time"=>$time);
				}
			}
		}
	}
	return $output;
}

?>