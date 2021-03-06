<?php
App::view($config['themes'].'/index');

$act = (isset($_GET['act'])) ? check($_GET['act']) : 'index';

if (is_admin([101, 102, 103])) {
    show_title('Денежные операции');

    switch ($act):
    ############################################################################################
    ##                                    Главная страница                                    ##
    ############################################################################################
        case 'index':

            $total = DB::run() -> querySingle("SELECT COUNT(*) FROM `transfers`;");
            $page = App::paginate(App::setting('listtransfers'), $total);

            if ($total > 0) {

                $querytrans = DB::run() -> query("SELECT * FROM `transfers` ORDER BY `time` DESC LIMIT ".$page['offset'].", ".$config['listtransfers'].";");

                while ($data = $querytrans -> fetch()) {
                    echo '<div class="b">';
                    echo '<div class="img">'.user_avatars($data['user']).'</div>';
                    echo '<b>'.profile($data['user']).'</b> '.user_online($data['user']).' ';

                    echo '<small>('.date_fixed($data['time']).')</small><br />';

                    echo '<a href="/admin/transfers?act=view&amp;uz='.$data['user'].'">Все переводы</a></div>';

                    echo '<div>';
                    echo 'Кому: '.profile($data['login']).'<br />';
                    echo 'Сумма: '.moneys($data['summ']).'<br />';
                    echo 'Комментарий: '.$data['text'].'<br />';
                    echo '</div>';
                }

                App::pagination($page);

                echo '<div class="form">';
                echo '<b>Поиск по пользователю:</b><br />';
                echo '<form action="/admin/transfers?act=view" method="get">';
                echo '<input type="hidden" name="act" value="view" />';
                echo '<input type="text" name="uz" />';
                echo '<input type="submit" value="Искать" /></form></div><br />';

                echo 'Всего операций: <b>'.$total.'</b><br /><br />';

            } else {
                show_error('Истории операций еще нет!');
            }
        break;

        ############################################################################################
        ##                                Просмотр по пользователям                               ##
        ############################################################################################
        case 'view':

            $uz = (isset($_GET['uz'])) ? check($_GET['uz']) : '';

            if (user($uz)) {

                $total = DB::run() -> querySingle("SELECT COUNT(*) FROM `transfers` WHERE `user`=?;", [$uz]);
                $page = App::paginate(App::setting('listtransfers'), $total);

                if ($total > 0) {

                    $queryhist = DB::run() -> query("SELECT * FROM `transfers` WHERE `user`=? ORDER BY `time` DESC LIMIT ".$page['offset'].", ".$config['listtransfers'].";", [$uz]);

                    while ($data = $queryhist -> fetch()) {
                        echo '<div class="b">';
                        echo '<div class="img">'.user_avatars($data['user']).'</div>';
                        echo '<b>'.profile($data['user']).'</b> '.user_online($data['user']).' ';

                        echo '<small>('.date_fixed($data['time']).')</small>';
                        echo '</div>';

                        echo '<div>';
                        echo 'Кому: '.profile($data['login']).'<br />';
                        echo 'Сумма: '.moneys($data['summ']).'<br />';
                        echo 'Комментарий: '.$data['text'].'<br />';
                        echo '</div>';
                    }

                    App::pagination($page);

                    echo 'Всего операций: <b>'.$total.'</b><br /><br />';

                } else {
                    show_error('Истории операций еще нет!');
                }
            } else {
                show_error('Ошибка! Данный пользователь не найден!');
            }

            echo '<i class="fa fa-arrow-circle-left"></i> <a href="/admin/transfers">Вернуться</a><br />';
        break;

    endswitch;

    echo '<i class="fa fa-wrench"></i> <a href="/admin">В админку</a><br />';

} else {
    redirect("/");
}

App::view($config['themes'].'/foot');
