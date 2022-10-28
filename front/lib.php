<?php

// Константы для подключения к базе данных
define("ServerName", "localhost");
define("UserLogin", "root");
define("UserPassword", "");
define("DbName", "puh");

// Функция для формирования описания товаров
function makeOpisan()
{
	// Подключаемся к MySQL
	$db = mysql_connect(ServerName,UserLogin,UserPassword);
	// Если подключение выполнено
	if ($db)
	{
		// Выбираем базу данных с проверкой успешного выбора
		if (mysql_select_db(DbName,$db))
		{
			// Указываем использование кодировки UTF-8
			mysql_query("SET names 'utf8'",$db);

			$rez = mysql_query("SELECT * FROM opisan",$db);

			$out_page = "";

			// Перебираем десять строк результата запроса
			for ($i=0;$i<10;$i++)
			{
				// Получаем очередную строку из запроса
				$row = mysql_fetch_row($rez);
					
				// Получаем наименование
				$naim = $row[1];
				// Получаем описание
				$txt = $row[2];

				// Добавляем количество в конец строки
				$out_page .= "<h1 class='h1_opisan'>{$naim}</h1><div class='div_opisan'>{$txt}</div><br/>";
			}

			// Выводим на страницу браузера
			echo $out_page;

		}
		// Отключаемся от MySQL
		mysql_close($db);
	}
}


// Получение номера раздела меню
function getIdMenu()
{
	// Результат
	$tmp=0;
	// Если есть данные в GET-запросе
	if ($_GET)
	{
	 // Если параметр: id имеет значение в GET-запросе
	 if ($_GET["id"])
	 {
	 	// Получаем значение параметра: id
	 	$tmp = $_GET["id"];
	 }
	 // Если это значение не лежит в интервале [1..3],
	 // то записываем нулеове значение в $tmp
	 if (!(($tmp>=1)&&($tmp<=3))) $tmp=0;
	}
	// Возвращаем значение из функции
	return $tmp;
}

// Получение HTML-разметки из базы данных и вывод на веб-страницу
function getHTML($nom)
{
	// Результат работы функции
	$html = "";
	// Подключаемся к MySQL
	$db = mysql_connect(ServerName,UserLogin,UserPassword);
	// Если подключение выполнено
	if ($db)
	{
		// Выбираем базу данных с проверкой успешного выбора
		if (mysql_select_db(DbName,$db))
		{
			// Указываем использование кодировки UTF-8
			mysql_query("SET names 'utf8'",$db);

			// Выполняем запрос для получения данных
			$rez = mysql_query("SELECT * FROM razmetka WHERE (id={$nom})");

			// Определяем количество строк в результате запроса
			$kol_str = mysql_num_rows($rez);

			// Если получена одна строка
			if ($kol_str==1)
			{
				// Получаем строку
				$row = mysql_fetch_row($rez);
				// Получаем значение поля: html
				$html = $row[1];
			}
		}
		// Отключаемся от MySQL
		mysql_close($db);
	}
	// Выводим HTML на страницу
	echo $html;
}

// Функция формирования таблицы
function makeTabl()
{
	// Подключаемся к MySQL
	$db = mysql_connect(ServerName,UserLogin,UserPassword);
	// Если подключение выполнено
	if ($db)
	{
		// Выбираем базу данных с проверкой успешного выбора
		if (mysql_select_db(DbName,$db))
		{
			// Указываем использование кодировки UTF-8
			mysql_query("SET names 'utf8'",$db);

			// Запрос для получения остатков товаров
			$sql1 = "SELECT naim, SUM(kol) FROM tovar GROUP BY naim ASC";
			// Выполняем запрос
			$rez1 = mysql_query($sql1,$db);

			// Запрос для получения наименований товаров
			$sql2 = "SELECT naim FROM opisan";
			// Выполняем запрос
			$rez2 = mysql_query($sql2,$db);

			// Переменная для вывода тегов на веб-страницу
			$out_page = "";

			// Перебираем десять строк результата запроса
			for ($i=0;$i<10;$i++)
			{
				// Получаем очередную строку из запроса
				$row1 = mysql_fetch_row($rez1);
					
				// Получаем очередную строку из запроса
				$row2 = mysql_fetch_row($rez2);

				// Получаем количество
				$kol = $row1[1];
				// Получаем наименование товара
				$naim = $row2[0];
					
				// Добавляем строку таблицы с двумя ячейками,
				// в ячейки помещаем наименование товара и количество
				$out_page .= "<tr><td>{$naim}</td><td>{$kol}</td></tr>";
			}

			// Выводим на страницу браузера
			echo $out_page;

		}
		// Отключаемся от MySQL
		mysql_close($db);
	}
}

// Функция обработки заявки
function testPost()
{
	// Если передача данных через POST
	if ($_POST)
	{
	// Если была нажата кнопка отправления данных
	if ($_POST["btn"])
	{
		// Переменная для сообщения
		$rez="";
		// Если поле Имя не заполнено
		if (trim($_POST["user_name"])=="") $rez="Введите Имя!";
		// Если поле Телефон не заполнено
		else if (trim($_POST["user_tel"])=="") $rez="Введите Телефон!";
		else
		{
			// Данные для записи в файл
			$info = "Name: ".trim($_POST["user_name"]).
			" Tel: ".trim($_POST["user_tel"]);

			// Создаем случайное имя файла
			$file_name = "".rand(1000000,9999999).".txt";

			// Сообщение о приеме заявки
			$rez="Заявка принята!";

			// Создаем файл для записи
			$f = fopen("./doc/".$file_name, "w");
			// Если файл создан
			if ($f)
			{
				// Записываем в него данные
				fwrite($f, $info);
				// Закрываем файл
				fclose($f);
			}
		}

		// Выводим сообщение на веб-страницу ввиде заголовка
		echo "<h1 class='h1_opisan' style='font-size: 35px;'>{$rez}</h1>";
	}
	}
}

?>
