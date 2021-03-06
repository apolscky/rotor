<?php
App::view($config['themes'].'/index');

if (isset($_GET['act'])) {
    $act = check($_GET['act']);
} else {
    $act = 'index';
}

show_title('Бан пользователя');

if (is_user()) {
    if ($udata['ban'] == 1) {
        if ($udata['timeban'] > SITETIME) {
            switch ($act):
            ############################################################################################
            ##                                    Главная страница                                    ##
            ############################################################################################
                case 'index':

                    echo '<i class="fa fa-times"></i> <b>Вас забанили</b><br /><br />';
                    echo '<b><span style="color:#ff0000">Причина бана: '.App::bbCode($udata['reasonban']).'</span></b><br /><br />';

                    echo 'До окончания бана осталось <b>'.formattime($udata['timeban'] - SITETIME).'</b><br /><br />';

                    echo 'Чтобы не терять время зря, рекомендуем вам ознакомиться с <b><a href="/rules">Правилами сайта</a></b><br /><br />';

                    echo 'Общее число строгих нарушений: <b>'.$udata['totalban'].'</b><br />';
                    echo 'Внимание, максимальное количество нарушений: <b>5</b><br />';
                    echo 'При превышении лимита нарушений ваш профиль автоматически удаляется<br />';
                    echo 'Востановление профиля или данных после этого будет невозможным<br />';
                    echo 'Будьте внимательны, старайтесь не нарушать больше правил<br /><br />';
                    // --------------------------------------------------//
                    if ($config['addbansend'] == 1 && $udata['explainban'] == 1) {
                        echo '<div class="form">';
                        echo '<form method="post" action="/ban?act=send">';
                        echo 'Объяснение:<br />';
                        echo '<textarea cols="25" rows="5" name="msg"></textarea><br />';
                        echo '<input value="Отправить" name="do" type="submit" /></form></div><br />';

                        echo 'Если модер вас забанил по ошибке или вы считаете, что бан не заслужен, то вы можете написать объяснение своего нарушения<br />';
                        echo 'В случае если ваше объяснение будет рассмотрено и удовлетворено, то возможно вас и разбанят<br /><br />';
                    }
                break;

                ############################################################################################
                ##                                    Отправка объяснения                                 ##
                ############################################################################################
                case 'send':

                    $msg = check($_POST['msg']);

                    if ($config['addbansend'] == 1) {
                        if ($udata['explainban'] == 1) {
                            if (utf_strlen($msg) >= 5 && utf_strlen($msg) < 1000) {
                                $queryuser = DB::run() -> querySingle("SELECT `id` FROM `users` WHERE `login`=? LIMIT 1;", [$udata['loginsendban']]);
                                if (!empty($queryuser)) {

                                    $msg = antimat($msg);

                                    $textpriv = 'Объяснение нарушения: '.$msg;

                                    DB::run() -> query("INSERT INTO `inbox` (`user`, `author`, `text`, `time`) VALUES (?, ?, ?, ?);", [$udata['loginsendban'], $log, $textpriv, SITETIME]);

                                    DB::run() -> query("UPDATE `users` SET `explainban`=? WHERE `login`=?;", [0, $log]);
                                    DB::run() -> query("UPDATE `users` SET `newprivat`=`newprivat`+1 WHERE `login`=?;", [$udata['loginsendban']]);

                                    notice('Объяснение успешно отправлено!');
                                    redirect("/ban");

                                } else {
                                    show_error('Ошибка! Пользователь который вас забанил не найден!');
                                }
                            } else {
                                show_error('Ошибка! Слишком длинное или короткое объяснение!');
                            }
                        } else {
                            show_error('Ошибка! Вы уже писали объяснение!');
                        }
                    } else {
                        show_error('Ошибка! Писать объяснительные запрещено админом!');
                    }

                    echo '<i class="fa fa-arrow-circle-left"></i> <a href="/ban">Вернуться</a><br />';
                break;

            endswitch;

        ############################################################################################
        ##                                    Конец бана                                          ##
        ############################################################################################
        } else {
            echo '<i class="fa fa-check"></i> <b>Срок бана закончился!</b><br /><br />';
            echo '<b><span style="color:#ff0000">Причина бана: '.App::bbCode($udata['reasonban']).'</span></b><br /><br />';

            echo 'Поздравляем!!! Время вашего бана вышло, постарайтесь вести себя достойно и не нарушать правила сайта<br /><br />';

            echo 'Рекомендуем ознакомиться с <b><a href="/rules">Правилами сайта</a></b><br />';

            echo 'Также у вас есть возможность исправиться и снять строгое нарушение.<br />';
            echo 'Если прошло более 1 месяца после последнего бана, то на странице <b><a href="/razban">Исправительная</a></b> заплатив штраф вы можете снять 1 строгое нарушение<br /><br />';

            DB::run() -> query("UPDATE `users` SET `ban`=?, `timeban`=?, `explainban`=? WHERE `login`=?;", [0, 0, 0, $log]);
        }
    } else {
        show_error('Ошибка! Вы не забанены или срок бана истек!');
    }
} else {
    show_error('Ошибка! Вы не авторизованы!');
}

App::view($config['themes'].'/foot');
