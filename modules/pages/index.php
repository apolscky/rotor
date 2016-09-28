<?php
App::view($config['themes'].'/index');

if (! preg_match('|^[a-z0-9_\-]+$|i', $act)) {
    App::abort(404);
}

if (! file_exists(DATADIR.'/main/'.$act.'.dat') || (! is_user() && $act == 'menu')){
    App::abort(404);
}

include (DATADIR.'/main/'.$act.'.dat');

App::view($config['themes'].'/foot');
