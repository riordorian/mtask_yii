<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ContactForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;

$this->title = 'Регистрация';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="site-login">
    <p>Заполните регистрационные данные:</p><?
    if( !empty($model->error) ){
        ?><div class="alert alert-danger"><?
            echo $model->error;
        ?></div><?
    }

    $form = ActiveForm::begin([
        'id' => 'registration-form',
        'fieldConfig' => [
            'template' => "{label}\n<div class=\"col-lg-3\">{input}</div>\n<div class=\"col-lg-8\">{error}</div><div class='clear'></div>",
            'labelOptions' => ['class' => 'col-lg-12 control-label'],
        ],
    ]); ?>
    <div class="row">
        <?= $form->field($model, 'username')->textInput(['autofocus' => true])->label('Логин'); ?>
        <?= $form->field($model, 'email')->label('Email'); ?>
        <?= $form->field($model, 'password')->passwordInput()->label('Пароль'); ?>
        <?= $form->field($model, 'passwordConfirm')->passwordInput()->label('Подтверждение пароля'); ?>

        <div class="form-group">
            <div class="col-lg-11">
                <?= Html::submitButton('Зарегестрироваться', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
            </div>
        </div>
    </div>
    <div class='clear'></div>

    <? ActiveForm::end(); ?>
</div>

