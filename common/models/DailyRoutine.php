<?php

namespace common\models;

use DateTime;
use Yii;

/**
 * This is the model class for table "daily_routine".
 *
 * @property int $id
 * @property int $user_id
 * @property int $kids_id
 * @property int $field1 Делали ли Вы зарядку? (0-нет,1-да)
 * @property int|null $field2 Продолжительность зарядки
 * @property int $field3 Занимались ли Вы вчера в кружках? (0-нет,1-да)
 * @property int|null $field4 Продолжительность кружка
 * @property int $field5 Занимались ли Вы вчера в спортивной секции?
 * @property int|null $field6 Продолжительность спорт секции 1
 * @property int|null $field7 Продолжительность спорт секции 2
 * @property int $field8 Продолжительность нахождения в пути до школы (пешком)
 * @property int $field9 Продолжительность нахождения в пути до школы (на транспорте)
 * @property int $field10 Продолжительность нахождения в пути из школы (пешком)
 * @property int $field11 Продолжительность нахождения в пути из школы (на транспорте)
 * @property int|null $field12 Суммарная продолжительность времени нахождения в пути в спортивную секцию, кружок (пешком)
 * @property int|null $field13 Суммарная продолжительность времени нахождения в пути в спортивную секцию, кружок (на транспорте)
 * @property int $field14 Гуляли ли Вы вчера на улице? (0-нет,1-да)
 * @property int|null $field15 Продолжительность прогулки (без учета времени нахождения в пути в школу и из школы, в спортивную секцию (кружок) и обратно
 * @property int $field16 Спали ли Вы днем? (0-нет,1-да)
 * @property int|null $field17 Продолжительность дневного сна
 * @property int $field18 Во сколько вы встали?
 * @property int $field19 Во сколько вы легли?
 * @property int $field20 Время начала занятий в школе
 * @property int $field21 Время окончания занятий в школе
 * @property string $created_at
 * @property string|null $updated_at
 */
class DailyRoutine extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'daily_routine';
    }

    public function rules()
    {
        $return []= [['field8', 'field9','field18', 'field19', 'field20', 'field21'], 'required'];
        $return []= [['field1', 'field2', 'field3', 'field4', 'field5', 'field6', 'field7', 'field8', 'field9', 'field14', 'field15', 'field16', 'field17'], 'integer'];
        $return []= [['field2','field4','field8','field15','field17'], 'integer', 'min' => 1, 'max'=>1440];
        $return []= [['field9'], 'integer', 'min' => 0, 'max'=>1440];
        $return []= [['field2','field4','field6','field7','field8','field9','field15','field17'], 'time_validation'];

        $kids = Kids::find()->where(['user_id'=>Yii::$app->user->id])->one();
        if ($kids->charging == 1)
        {
            $return []= [['field2'], 'required'];
        }
        if ($kids->additional_education == 1)
        {
            $return []= [['field4'], 'required'];
        }
        if ($kids->sports_section1 > 0)
        {
            $return []= [['field6'], 'required'];
            $return []= [['field6'], 'integer', 'min' => 25, 'max'=>1440];
        }
        if ($kids->sports_section2 > 0)
        {
            $return []= [['field7'], 'required'];
            $return []= [['field7'], 'integer', 'min' => 25, 'max'=>1440];
        }
        if ($kids->walk == 1)
        {
            $return []= [['field15'], 'required'];
        }
        if ($kids->sleep_day == 1)
        {
            $return []= [['field17'], 'required'];
        }

        return $return;
    }

    public function time_validation($attribute) //проверка на превшение времени в сутках
    {
        $start_date = new DateTime('2021-05-02 '.$this->field19);
        $since_start = $start_date->diff(new DateTime('2021-05-03 '.$this->field18));
        $sleep_min = $since_start->i+($since_start->h*60); //время сна в минутах

        $start_date2 = new DateTime('2021-05-02 '.$this->field20);
        $since_start2 = $start_date2->diff(new DateTime('2021-05-03 '.$this->field21));
        $school_min = $since_start2->i+($since_start2->h*60); //время нахождения в школе в минутах

        $total = $sleep_min+$school_min+$this->field2+$this->field4+$this->field6+$this->field7+$this->field8+$this->field9+$this->field15+$this->field17;

        if ($total > 1440)
        {
            //$this->addError($attribute, $total);
            $this->addError($attribute, 'Проверьте правильность внесённого времени (превышает 24 часа).');
        }
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'kids_id' => 'Kids ID',
            'field1' => 'Делали ли Вы зарядку?',
            'field2' => 'Укажите продолжительность зарядки в минутах',
            'field3' => 'Занимались ли Вы вчера в кружках?',
            'field4' => 'Укажите продолжительность кружка (студии) в минутах',
            'field5' => 'Занимались ли Вы в спортивной секции?',
            'field6' => 'Укажите продолжительность спортивной секции',
            'field7' => 'Укажите продолжительность спортивной секции',
            'field8' => 'Пешком', //Продолжительность нахождения в пути до школы (пешком)
            'field9' => 'На транспорте', //Продолжительность нахождения в пути до школы (на транспорте)
            //'field10' => 'Пешком', //Продолжительность нахождения в пути из школы (пешком)
            //'field11' => 'На транспорте', //Продолжительность нахождения в пути из школы (на транспорте)
            //'field12' => 'Пешком', //Суммарная продолжительность времени нахождения в пути в спортивную секцию, кружок (пешком)
            //'field13' => 'На транспорте', //Суммарная продолжительность времени нахождения в пути в спортивную секцию, кружок (на транспорте)
            'field14' => 'Гуляли ли Вы на улице?',
            'field15' => 'Укажите продолжительность прогулки в минутах',
            'field16' => 'Спали ли Вы днем?',
            'field17' => 'Укажите продолжительность дневного сна в минутах',
            'field18' => 'Во сколько вы встали?',
            'field19' => 'Во сколько вы легли?',
            'field20' => 'Время начала занятий в школе/дет.саду',
            'field21' => 'Время окончания занятий в школе/дет.саду',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
