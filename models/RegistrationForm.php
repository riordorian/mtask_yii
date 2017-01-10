<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\web\User;

/**
 * ContactForm is the model behind the contact form.
 */
class RegistrationForm extends Model
{
    public $username;
    public $email;
    public $password;
    public $passwordConfirm;
    public $error;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // name, email, subject and body are required
            [['username', 'email', 'password', 'passwordConfirm'], 'required'],
            // email has to be a valid email address
            ['email', 'email'],
            [['password', 'passwordConfirm'], 'validatePassword'],
        ];
    }


    /**
     * Регистрация пользователя
     *
     * @return bool
     */
    public function register()
    {
        if( $this->validate() ){
            $obUser = Users::findByCustCondition('OR', ['username' => $this->username, 'email' => $this->email]);
            if( !empty($obUser) ){
                $this->error = 'Пользователь с такими данными уже зарегистрирован';

                return false;
            }
            try{
                Users::register([
                    'username' => $this->username,
                    'email' => $this->email,
                    'password' => $this->password,
                ]);

                Yii::$app->user->login(Users::findOne(['username' => $this->username]), 0);

                return true;
            }
            catch(\Exception $e){
                $this->error = $e->getMessage();
                return false;
            }
        }
        else{
            return false;
        }
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute)
    {
        if( $attribute == 'passwordConfirm' && $this->passwordConfirm != $this->password ){
            $this->addError($attribute, 'Incorrect password confirm field value');
        }
        elseif( $attribute == 'password' && strlen($this->password) < 6 ){
            $this->addError($attribute, 'Password min length - 6 characters');
        }
    }
}
