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
    ?><ul class="sortable-list connectList agile-list ui-sortable" id="todo">
        <li class="success-element">
            Перейдите по ссылке для получения <a target="_blank" href="https://alxtest.bitrix24.ru/oauth/authorize/?client_id=<?=$arResult['client_id']?>&response_type=code"><strong>кода</strong></a>
        </li>
        <li class="success-element">
            <p><strong>Затем введите полученный код в поле</strong></p>
            <div class="form-group">
				<label>Код</label>
				<input type="text" name="code" placeholder="Введите полученный код" class="form-control">
				<input type="submit" class="btn btn-md btn-primary m-t">
			</div>
        </li>
    </ul><?
}


