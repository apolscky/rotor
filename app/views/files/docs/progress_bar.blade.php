<?php show_title('Функция progress_bar'); ?>

Функция выводит прогресс-бар, на основе введенных данных (Доступно с версии 3.0.2)<br /><br />

<pre class="d">
<b>progress_bar</b>(
	int percent,
	string title = ''
);
</pre><br />

<b>Параметры функции</b><br />

<b>percent</b> - Количество процентов в графике (от 0 до 100)<br />
<b>title</b> - Альтернативное название (По умолчанию пусто), если данные не переданы, то название берется из параметра percent<br /><br />

<b>Примеры использования</b><br />

<?php
echo App::bbCode(check('[code]<?php
progress_bar(55);
progress_bar(35, \'Батарейка\');
/* Результат выполнения функции представлен ниже */
?>[/code]'));

progress_bar(55).'<br />';
progress_bar(35, 'Батарейка').'<br />';
?>

<br />
<i class="fa fa-arrow-circle-left"></i> <a href="/files/docs">Вернуться</a><br />
