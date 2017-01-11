<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
$this->title = 'Авторизация';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-login">
    <p>Введите данные для авторизации:</p>

    <?php
    $form = ActiveForm::begin([
        'id' => 'login-form',
        'layout' => 'horizontal',
        'fieldConfig' => [
            'template' => "<div class=\"col-lg-12\">{input}</div>",
        ],
    ]);?>
    
        <?= $form->field($model, 'username')->textInput(['autofocus' => true, 'placeholder' => 'Логин']); ?>

        <?= $form->field($model, 'password')->passwordInput(['placeholder' => 'Пароль']); ?>

        <?= $form->field($model, 'rememberMe')->checkbox([
            'template' => "<label class='checkbox-label'><div class=\"col-lg-12 icheckbox_square-green\">{input}</div>&nbsp;&nbsp;{labelTitle}</label>",
            'class' => 'widget i-check'
        ])->label('Запомнить меня') ?>

        <div class="form-group">
            <div class="col-lg-12">
                <?= Html::submitButton('Войти', ['class' => 'btn btn-primary block full-width', 'name' => 'login-button']) ?>
            </div>
        </div>

        <a href="/reset-password/"><small>Забыли пароль?</small></a>
        <p class="text-muted text-center"><small>Еще не зарегистрированы?</small></p>
        <a class="btn btn-sm btn-white btn-block" href="/regitration/">Создать учетную запись</a>

    <?php ActiveForm::end(); ?>
</div>
