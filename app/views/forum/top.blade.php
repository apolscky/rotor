@extends('layout')

@section('title')
    Топ популярных тем - @parent
@stop

@section('content')

    <h1>Топ популярных тем</h1>

    <a href="/forum">Форум</a>

    <?php foreach ($topics as $data): ?>
        <div class="b">
            <i class="fa <?= $data->getIcon() ?> text-muted"></i>
            <b><a href="/topic/<?=$data['id']?>"><?=$data['title']?></a></b> (<?=$data['posts']?>)
        </div>
        <div>
            <?= Forum::pagination($data)?>
            Автор: <?=$data->getUser()->login ?><br />
            Сообщение: <?= $data->getLastPost()->getUser()->login ?> (<?=date_fixed($data->getLastPost()->created_at)?>)
        </div>
    <?php endforeach; ?>

    <?php App::pagination($page) ?>
@stop
