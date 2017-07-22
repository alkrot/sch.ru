/* document.getElementById("numberGroup").onkeyup = function(e) {
  print(this);  
} */
document.getElementById("numberGroup").oninput = function() {
    loadCurs(this);
}

document.getElementById("newSchedule").onclick = function(){
	refresh();
}

function print(input,jsonString){
	if (input.value.length < 3 || input.value.length > 3 || jsonString == 'error') return false;
    clearTable();
    if (jsonString[input.value]) {
		var group = jsonString[input.value];
        for (var key in group) {
            var table = document.getElementById("subjects");
            var newrow = table.insertRow(-1);
            var cell1 = newrow.insertCell(-1);
            cell1.innerHTML = daysru(key) + ", " + group[key]['date'] + inputDate(group[key]['date'],key);
            cell1.setAttribute("colspan", "3", 0);
            for (var ke in group[key]['time']) {
                newrow = table.insertRow(-1);
                cell1 = newrow.insertCell(-1);
                var ceil2 = newrow.insertCell(-1);
                var ceil3 = newrow.insertCell(-1);
                cell1.innerHTML = inputTimes(ke, key);
                ceil2.innerHTML = inputSubjects(key, group[key]['time'][ke].name);
                ceil3.innerHTML = inputCabinet(key, group[key]['time'][ke].cabinet)
            }
        }
    } else {
        alert("Такой группы нет")
    }
}

function refresh(){
	var numberGroup = document.getElementById("numberGroup");
	loadCurs(numberGroup);
}

function saveJson() {
    var msg = $("#formx").serialize();
    $.ajax({
        type: 'POST',
        url: 'php/edit.php',
        data: msg,
        success: function(data) {
            //console.log(data);
			alert(data);
        },
        error: function(xhr, str) {
            alert('Возникла ошибка: ' + xhr.responseCode)
        }
    })
}
function inputDate(date, day){
	var get = '<input type="hidden" name="jsonString[\'' + day + '\'][\'date\']" value=' + date + '>';
    return get
}
function inputTimes(time, day) {
    var get = '<input size="10" type="text" name="jsonString[\'' + day + '\'][\'time\'][]" value=' + time + '>';
    return get
}

function inputSubjects(day, name) {
    name = (name == undefined) ? "" : name;
    var get = '<input type="text" size="50" value="' + name + '" name="jsonString[\'' + day + '\'][\'subjects\'][]">';
    return get
}

function inputCabinet(day, cabinet) {
    cabinet = (cabinet == undefined) ? "" : cabinet;
    var get = '<input type="text" size="1" value="' + cabinet + '" name="jsonString[\'' + day + '\'][\'cabinet\'][]">';
    return get
}

function loadCurs(input) {
	var newSchedule  = document.getElementById('newSchedule').checked;
    var curs = (newSchedule) ? input.value[0] + 'n' : input.value[0];
    if (curs) {
		$.get('../php/check.php',{ curs: curs },function(data){
						data = JSON.parse(data);
						if(data['error']){
							jsonString = 'error';
							alert(data['error']);
						}
						else print(input,data);
		});
    }
}

function daysru(day) {
    var days = {
        "Sunday": "Восскресенье",
        "Monday": "Понедельник",
        "Tuesday": "Вторник",
        "Wednesday": "Среда",
        "Thursday": "Четверг",
        "Friday": "Пятница",
        "Saturday": "Суббота"
    }
    return days[day]
}

function clearTable() {
    for (var i = document.getElementById('subjects').getElementsByTagName('tr').length - 1; i; i--) {
        document.getElementById('subjects').deleteRow(i)
    }
}