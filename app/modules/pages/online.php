<?php
App::view($config['themes'].'/index');

show_title('Кто в онлайне');

$total     = Online::whereNotNull('user_id')->count();
$total_all = Online::count();

echo 'Всего на сайте: <b>'.$total_all.'</b><br />';
echo 'Зарегистрированных:  <b>'.$total.'</b><br /><br />';

switch ($act):
############################################################################################
##                                    Главная страница                                    ##
############################################################################################
    case 'index':

        $page = App::paginate(App::setting('onlinelist'), $total);
        if ($total > 0) {

            $online = Online::whereNotNull('user_id')
                ->with('user')
                ->orderBy('updated_at', 'desc')
                ->offset($page['offset'])
                ->limit($config['onlinelist'])
                ->get();

            foreach ($online as $data) {
                echo '<div class="b">';
                echo user_gender($data->user).' <b>'.profile($data->user).'</b> (Время: '.date_fixed($data['updated_at'], 'H:i:s').')</div>';

                if (is_admin() || empty($config['anonymity'])) {
                    echo '<div><span class="data">('.$data['brow'].', '.$data['ip'].')</span></div>';
                }
            }

            App::pagination($page);
        } else {
            show_error('Авторизованных пользователей нет!');
        }

        echo '<i class="fa fa-users"></i> <a href="/online/all">Показать гостей</a><br />';
    break;

    ############################################################################################
    ##                                Список всех пользователей                               ##
    ############################################################################################
    case 'all':

        $total = $total_all;
        $page = App::paginate(App::setting('onlinelist'), $total);

        if ($total > 0) {

            $online = Online::with('user')
                ->orderBy('updated_at', 'desc')
                ->offset($page['offset'])
                ->limit($config['onlinelist'])
                ->get();

            foreach ($online as $data) {
                if (empty($data['user'])) {
                    echo '<div class="b">';
                    echo '<i class="fa fa-user-circle-o"></i> <b>'.$config['guestsuser'].'</b>  (Время: '.date_fixed($data['updated_at'], 'H:i:s').')</div>';
                } else {
                    echo '<div class="b">';
                    echo user_gender($data->user).' <b>'.profile($data->user).'</b> (Время: '.date_fixed($data['updated_at'], 'H:i:s').')</div>';
                }

                if (is_admin() || empty($config['anonymity'])) {
                    echo '<div><span class="data">('.$data['brow'].', '.$data['ip'].')</span></div>';
                }
            }

            App::pagination($page);
        } else {
            show_error('На сайте никого нет!');
        }

        echo '<i class="fa fa-users"></i> <a href="/online">Скрыть гостей</a><br />';
    break;

endswitch;

App::view($config['themes'].'/foot');
