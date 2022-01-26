<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\rbac\DbManager;


class FactdateForm extends Model
{
    public $menu_id;
    public $date;
    public $date_end;
    public $display_normativ;
    public $display_him_feed;
    public $brutto_netto;
    /*public $count;*/


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['menu_id', 'date', 'date_end', 'display_normativ', 'display_him_feed', 'brutto_netto'], 'required'],
            [['menu_id'], 'integer'],

            //['date', 'validatePassword'],

        ];
    }

    public function attributeLabels()
    {
        return [
            'menu_id' => 'Меню',
            'date' => 'Дата',
            'date_end' => 'Дата завершения',
            'brutto_netto' => 'Брутто/Нетто',
        ];
    }

    public function validatePassword()
    {

        $this->addError('date', 'Неправильное имя пользователя или пароль.');

    }
}
