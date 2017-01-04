<?php

/* @var $this yii\web\View */

use yii\helpers\Html;

$this->title = 'Получение кода';
$this->params['breadcrumbs'][] = $this->title;
$arGet = Yii::$app->request->get();

if( !empty($arGet['code']) ){
    Yii::$app->response->redirect('/site/loader');
}
else{
    ?><div class="site-about">
    <h1><?= Html::encode($this->title) ?></h1><?


    ?><ol>
        <li>Перейдите по ссылке для получения <a target="_blank" href="https://alxtest.bitrix24.ru/oauth/authorize/?client_id=<?=$arResult['client_id']?>&response_type=code">кода</a></li>
        <li>
            <p>Затем</p>
            <form action="">
                <input type="text" name="code" placeholder="Введите полученный код">
                <input type="submit">
            </form>
        </li>
    </ol>
    </div><?
}


