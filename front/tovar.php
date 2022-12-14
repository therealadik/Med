<?php
// Возврат количества товаров 
// в Java приложение
function javaKol()
{	
	// Переменная в которой будет результат остатков
	$otvet = "";
	
	// Запрос для подсчета остатков
	$sql = "SELECT naim, SUM(kol) FROM tovar GROUP BY naim ASC";
	// Выполним запрос и получим результат
	$rez = mysql_query($sql);

	// Перебираем десять строк результат запроса
    for ($i=0;$i<10;$i++)
    {
       // Получаем очередную строку из запроса 
       $row = mysql_fetch_row($rez);
       // Получаем количество
       $kol = $row[1];
       // Доавляем количество в конец строки
       $otvet .= $kol;
       // Добавляем разделитель тильда (~) 
       if ($i!=9) $otvet .= "~"; 
    }  

    // Выводим на страницу браузера
    echo $otvet;
    
}
// Функция формирования SQL-запроса на добавление поступления товаров
function getQuery()
{	
	// Получаем дату и время на момент записи в базу данных 
	$date_time = date('Y-m-d [H:i:s]');	
	// Перебираем в цикле десять параметров 
	for ($i=1;$i<=10;$i++)
	{
       // Переменная для имени параметра
	   $tmp = "tov".$i;	   		
	   // Если такой параметр передан в GET-запросе
	   if ($_GET[$tmp])
	   {
	   	   // Значение текущего параметра - количество товара
	   	   $kol = $_GET[$tmp];
           // Строка запроса
	   	   $q = "INSERT INTO tovar (naim,kol,dv)".
	   	        "VALUES ({$i},{$kol},'{$date_time}')";
	   	   // Выполянем запрос
	   	   mysql_query($q);	   	   
	   }	
	}
}
// Выполнение операций с базой данных MySQL
function runMySQL()
{
// Подключаемся к MySQL
$db = mysql_connect("localhost","root","");
// Если подключение выполнено
if ($db)
{
	// Выбираем базу данных с проверкой успешного выбора
    if (mysql_select_db("puh",$db))
    {  
       // Указываем использование кодировки UTF-8  	    	
       mysql_query("SET names 'utf8'",$db);
       
       // Запрос на добавление данных из GET-запроса
       getQuery();
       // Возврат количества товаров 
       javaKol();                    
    }
    // Отключаемся от MySQL
    mysql_close($db);    
}
}

// Если есть данные в GET-запросе
if ($_GET)
{
	// ВЫПОЛНЯЕМ ПРОВЕРКУ ФОРМАТА GET-ЗАПРОСА
    // Флаг проверки данных в GET-запросе	
	$flag = false;	
	// Цикл, выполняющий десять шагов	
	for ($i=1;$i<=10;$i++)
	{
	   // Переменная для имени параметра
	   $tmp = "tov".$i;
	   // Если такой параметр передан в GET-запросе
	   if ($_GET[$tmp])
	   {
	   	  // Меняем значение флага
	      $flag=true;
	      // Прерываем цикл
	      break;   	  
	   }	
	}
	
	// Если формат GET-запроса оказался верным
	if ($flag)
	{
		runMySQL();
	}
}
?>
