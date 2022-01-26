<?php

namespace common\models;

use Yii;
use yii\base\Model;

class DateForm extends Model
{
    public $date_start;
    public $date_end;
    public $field;
    public $field2;
    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['field', 'field2'], 'required'],
            [['date_start', 'date_end'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'organization' => 'Организация',
        ];
    }

}
