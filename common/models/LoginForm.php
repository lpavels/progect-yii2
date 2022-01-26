<?php

namespace common\models;

use Yii;
use yii\base\Model;

class LoginForm extends Model
{
    public $login;
    public $rememberMe = true;
    private $_user;

    public function rules()
    {
        return [
            [['login'], 'required'],
            ['login', 'validateLogin'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'login' => 'Идентификационный ключ',
        ];
    }

    public function validateLogin($attribute, $params)
    {
        if (!$this->hasErrors())
        {
            $user = $this->getUserByLogin();
            if (!$user)
            {
                $this->addError($attribute, 'Неверный идентификационный ключ.');
            }
        }
    }

    public function login()
    {
        if ($this->validate())
        {
            return Yii::$app->user->login($this->getUserByLogin(), $this->rememberMe ? 3600 * 24 * 30 : 0);
        }

        return false;
    }

    protected function getUserByLogin()
    {
        if ($this->_user === null)
        {
            $this->_user = User::findByLogin($this->login);
        }

        return $this->_user;
    }
}
