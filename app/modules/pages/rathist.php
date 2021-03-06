<?php
App::view($config['themes'].'/index');

if (isset($_GET['act'])) {
    $act = check($_GET['act']);
} else {
    $act = 'received';
}
if (empty($_GET['uz'])) {
    $uz = check($log);
} else {
    $uz = check(strval($_GET['uz']));
}

show_title('История голосований '.$uz);

if (is_user()) {
    $is_admin = is_admin([101]);

    $data = DB::run() -> queryFetch("SELECT * FROM `users` WHERE `login`=? LIMIT 1;", [$uz]);

    if (!empty($data)) {

        switch ($act):
        ############################################################################################
        ##                                    Полученные голоса                                   ##
        ############################################################################################
            case 'received':
                echo '<i class="fa fa-thumbs-up"></i> <b>Полученные</b> / <a href="/rathist?act=gave&amp;uz='.$uz.'">Отданные</a><hr />';

                $queryrat = DB::run() -> query("SELECT * FROM `rating` WHERE `login`=? ORDER BY `time` DESC LIMIT 20;", [$uz]);
                $rat = $queryrat -> fetchAll();

                if (count($rat) > 0) {
                    if ($is_admin) {
                        echo '<form action="/rathist?act=del&amp;uz='.$uz.'&amp;uid='.$_SESSION['token'].'" method="post">';
                    }

                    foreach($rat as $data) {
                        echo '<div class="b">';

                        if ($is_admin) {
                            echo '<input type="checkbox" name="del[]" value="'.$data['id'].'" /> ';
                        }

                        if (empty($data['vote'])) {
                            echo '<i class="fa fa-thumbs-down text-danger"></i> ';
                        } else {
                            echo '<i class="fa fa-thumbs-up text-success"></i> ';
                        }

                        echo '<b>'.profile($data['user']).'</b> ('.date_fixed($data['time']).')</div>';
                        echo '<div>Комментарий: ';

                        if (!empty($data['text'])) {
                            echo App::bbCode($data['text']);
                        } else {
                            echo 'Отсутствует';
                        }

                        echo '</div>';
                    }

                    if ($is_admin) {
                        echo '<br /><input type="submit" value="Удалить выбранное" /></form>';
                    }

                    echo '<br />';
                } else {
                    show_error('В истории еще ничего нет!');
                }
            break;

            ############################################################################################
            ##                                      Отданные голоса                                   ##
            ############################################################################################
            case 'gave':
                echo '<i class="fa fa-thumbs-up"></i> <a href="/rathist?act=received&amp;uz='.$uz.'">Полученные</a> / <b>Отданные</b><hr />';

                $queryrat = DB::run() -> query("SELECT * FROM `rating` WHERE `user`=? ORDER BY `time` DESC LIMIT 20;", [$uz]);
                $rat = $queryrat -> fetchAll();

                if (count($rat) > 0) {
                    foreach($rat as $data) {
                        echo '<div class="b">';
                        if (empty($data['vote'])) {
                            echo '<i class="fa fa-thumbs-down text-danger"></i> ';
                        } else {
                            echo '<i class="fa fa-thumbs-up text-success"></i> ';
                        }

                        echo '<b>'.profile($data['login']).'</b> ('.date_fixed($data['time']).')</div>';
                        echo '<div>Комментарий: ';

                        if (!empty($data['text'])) {
                            echo App::bbCode($data['text']);
                        } else {
                            echo 'Отсутствует';
                        }

                        echo '</div>';
                    }

                    echo '<br />';
                } else {
                    show_error('В истории еще ничего нет!');
                }
            break;

            ############################################################################################
            ##                                     Удаление истории                                   ##
            ############################################################################################
            case 'del':

                $uid = check($_GET['uid']);
                if (isset($_POST['del'])) {
                    $del = intar($_POST['del']);
                } else {
                    $del = 0;
                }

                if (is_admin([101])) {
                    if ($uid == $_SESSION['token']) {
                        if (!empty($del)) {
                            $del = implode(',', $del);

                            DB::run() -> query("DELETE FROM `rating` WHERE `id` IN (".$del.") AND `login`=?;", [$uz]);

                            notice('Выбранные голосования успешно удалены!');
                            redirect("/rathist?uz=$uz");
                        } else {
                            show_error('Ошибка! Отсутствуют выбранные голосования!');
                        }
                    } else {
                        show_error('Ошибка! Неверный идентификатор сессии, повторите действие!');
                    }
                } else {
                    show_error('Ошибка! Удалять голосования могут только модераторы!');
                }

                echo '<i class="fa fa-arrow-circle-left"></i> <a href="/rathist?uz='.$uz.'">Вернуться</a><br />';
            break;

        endswitch;

    } else {
        show_error('Ошибка! Пользователь с данным логином  не зарегистрирован!');
    }
} else {
    show_login('Вы не авторизованы, чтобы просматривать историю, необходимо');
}

echo '<i class="fa fa-arrow-circle-up"></i> <a href="/user/'.$uz.'">В анкету</a><br />';

App::view($config['themes'].'/foot');
