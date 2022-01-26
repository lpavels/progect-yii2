<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "normativ_vitamin_day".
 *
 * @property int $id
 * @property string $name
 * @property int $age_info_id
 * @property float $value
 * @property string $created_at
 */
class NormativVitaminDay extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'normativ_vitamin_day';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'name', 'age_info_id', 'value'], 'required'],
            [['id', 'age_info_id'], 'integer'],
            [['value'], 'number'],
            [['created_at'], 'safe'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Витамин/Минерал',
            'age_info_id' => 'Возрастная группа',
            'value' => 'Значение',
            'created_at' => 'Дата внесения в программу',
        ];
    }


    public function get_name($name){

        $vitamins = [
            'vitamin_a' => 'Витамин А',
            'vitamin_b1' => 'Витамин В1',
            'vitamin_b2' => 'Витамин В2',
            'vitamin_c' => 'Витамин С',
            'vitamin_d' => 'Витамин D',
            'vitamin_pp' => 'Витамин PP',

            'na' => 'na',
            'k' => 'k',
            'ca' => 'ca',
            'mg' => 'mg',
            'p' => 'p',
            'f' => 'f',
            'fe' => 'fe',
            'i' => 'i',
            'se' => 'se',
        ];

        return $vitamins[$name] ;
    }


}
