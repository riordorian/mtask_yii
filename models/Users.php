<?php

namespace app\models;

use yii\base\Exception;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use yii\base\NotSupportedException;
use Yii;

class Users extends ActiveRecord implements IdentityInterface
{
    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        $obUser = static::findOne(['username' => $username]);
        return $obUser;
    }

    /**
     * Finds user by condition
     *
     * @param string $condition
     * @param array $arFields
     * @return static|null
     */
    public static function findByCustCondition($condition = 'AND', $arFields = [])
    {
        $obUser = static::find();

        $i = 0;
        foreach( $arFields as $fieldName => $fieldVal ){
            if( $i == 0 ){
                $obUser->where(['=', $fieldName, $fieldVal]);
            }
            else{
                if( $condition == 'AND' ){
                    $obUser->andWhere(['=', $fieldName, $fieldVal]);
                }
                elseif( $condition == 'OR' ){
                    $obUser->orWhere(['=', $fieldName, $fieldVal]);
                }
            }

            $i++;
        }

        return $obUser->one();
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->authKey;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password);
    }


    /**
     * Add new user to DB
     *
     * @param $arFields - array of user fields
     *
     * @return bool
     * @throws Exception
     */
    public static function register($arFields)
    {
        if( empty($arFields['username']) || empty($arFields['email']) || empty($arFields['password']) ){
            throw new Exception('Incorrect user params');
        }
        
        $obUser = new Users();
        foreach($arFields as $fieldName => $fieldVal){
            if( $fieldName == 'password' ){
                $fieldVal = Yii::$app->security->generatePasswordHash($arFields['password']);
            }

            $obUser->{$fieldName} = $fieldVal;
        }

        if( $obUser->save() ){
            return true;
        }
        else{
            throw new Exception('DB insert error');
        }
    }
}
