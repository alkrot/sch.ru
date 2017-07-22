<html>
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no" />
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<meta name="format-detection" content="telephone=no" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<meta name="MobileOptimized" content="176" />
		<meta name="HandheldFriendly" content="True" />
		<title>Расписание для старых мобильников</title>
		<style>
			html, body {  font-size: large; margin: 0;  text-align: center; }
			#ng {margin-right:5px;word-wrap:break-word;text-align: center;}
			.sh {border:1px solid black;margin-top:3px;padding-top:3px;margin-right: 5px;margin-left:5px;padding-bottom:3px;width:auto;}
		</style>
	</head>
	<body>
		<div>
			<form method="POST">
				<span>Номер группы: </span><input id="ng" type="text" name="numbergroup">
				<input type="submit" value="Посмотреть">
			</form>
		</div>
		<?
			$daysru = array ("Monday"=>"Понедельник", "Tuesday"=>"Вторник","Wednesday"=>"Среда","Thursday"=>"Четверг","Friday"=>"Пятница","Saturday"=>"Суббота");
			
				echo '<div id = "shedule"><br/>';
				if(isset($_POST['numbergroup'])){
					$ng = $_POST['numbergroup'];
					$file = '../json/'.$ng[0].'.json';
					if(!file_exists($file)){
						echo "Такого курса нет";
						return;
					}
					$json = file_get_contents($file);
					if($json){
						$array = json_decode($json,true);
						if(!$array[$ng]){
							echo "Такой группы нет";
							return false;
						}
						echo $ng;
						foreach($array[$ng] as $daysWeek => $value){
							echo "<div class='sh'>";
							echo $daysru[$daysWeek]." [".$value["date"]."]</br>";
							foreach($array[$ng][$daysWeek]["time"] as $time => $v){
								if($v["name"]) echo $time." | ".$v["name"].(($v["cabinet"]) ? " | ".$v["cabinet"] : "")."<br/>";
							}
							echo "</div>";
						}
					}
					
				}
				echo "</div>";
		?>
	</body>
</html>