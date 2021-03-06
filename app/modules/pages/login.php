<?php

$domain = check_string($config['home']);

switch ($act):
############################################################################################
##                                       Авторизация                                      ##
############################################################################################
case 'index':

    if (is_user()) {
        App::abort('403', 'Вы уже авторизованы!');
    }

    $cooklog = (isset($_COOKIE['login'])) ? check($_COOKIE['login']): '';
    if (Request::isMethod('post')) {
        if (Request::has('login') && Request::has('pass')) {
            $return = Request::input('return', '');
            $login = check(utf_lower(Request::input('login')));
            $pass = trim(Request::input('pass'));
            $remember = Request::input('remember');

            if ($user = App::login($login, $pass, $remember)) {
                notice('Добро пожаловать, '.$login.'!');

                if ($return) {
                    App::redirect($return);
                } else {
                    App::redirect('/');
                }
            }

            App::setInput(Request::all());
            App::setFlash('danger', 'Ошибка авторизации. Неправильный логин или пароль!');
        }

        if (Request::has('token')) {
            App::socialLogin(Request::input('token'));
        }
    }

    App::view('pages/login', compact('cooklog'));
break;
############################################################################################
##                                           Выход                                        ##
############################################################################################
case 'logout':

    $_SESSION = [];
    setcookie('password', '', time() - 3600, '/', $domain, null, true);
    setcookie(session_name(), '', time() - 3600, '/', '');
    session_destroy();

    App::redirect('/');
break;

endswitch;
