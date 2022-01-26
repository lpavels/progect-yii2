<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "report_tbl21".
 *
 * @property int $id
 * @property int $user_id
 * @property int $federal_district_id
 * @property int $region_id
 * @property int $municipality_id
 * @property int $organization_id id организации
 * @property int $training_id 1-школьная, 2- дошкольная
 * @property int $type_training взрослый(2) /ребенок (1)
 * @property string $key_login Уникальный номер
 * @property string $type_listener Тип слушателя
 * @property int|null $class_number Класс
 * @property string|null $letter_number буква класса
 * @property int|null $input_test Входной балл
 * @property int|null $theme1 Школьная программа - 5 тем, дошкольная программа - 6 тем
 * @property int|null $theme2 Школьная программа - 5 тем, дошкольная программа - 6 тем
 * @property int|null $theme3 Школьная программа - 5 тем, дошкольная программа - 6 тем
 * @property int|null $theme4 Школьная программа - 5 тем, дошкольная программа - 6 тем
 * @property int|null $theme5 Школьная программа - 5 тем, дошкольная программа - 6 тем
 * @property int|null $theme6 Школьная программа - 5 тем, дошкольная программа - 6 тем			
 * @property int|null $independent_work Самостоятельная работа
 * @property int|null $final_test Итоговый балл (%)
 * @property int|null $final_test_1st Итоговый тест пройден с 1-ого раза
 * @property int|null $final_test_2st Итоговый тест пройден со 2-ого раза или более
 * @property int|null $training_completed Обучение завершено
 * @property int|null $number_children Внесено детей воспитателем
 * @property string $created_at
 * @property string|null $updated_at
 * @property string $created_ip
 * @property string|null $updated_ip
 */
class ReportTbl21 extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'report_tbl21';
    }

    public function rules()
    {
        return [
            [['user_id', 'federal_district_id', 'region_id', 'municipality_id', 'organization_id', 'training_id', 'type_training', 'key_login', 'type_listener', 'created_ip'], 'required'],
            [['user_id', 'federal_district_id', 'region_id', 'municipality_id', 'organization_id', 'training_id', 'type_training', 'class_number', 'input_test', 'theme1', 'theme2', 'theme3', 'theme4', 'theme5', 'theme6', 'independent_work', 'final_test', 'final_test_1st', 'final_test_2st', 'training_completed', 'number_children'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['key_login', 'type_listener'], 'string', 'max' => 250],
            [['letter_number'], 'string', 'max' => 10],
            [['created_ip', 'updated_ip'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'federal_district_id' => 'Federal District ID',
            'region_id' => 'Region ID',
            'municipality_id' => 'Municipality ID',
            'organization_id' => 'Organization ID',
            'training_id' => 'Training ID',
            'type_training' => 'Type Training',
            'key_login' => 'Key Login',
            'type_listener' => 'Type Listener',
            'class_number' => 'Class Number',
            'letter_number' => 'Letter Number',
            'input_test' => 'Input Test',
            'theme1' => 'Theme1',
            'theme2' => 'Theme2',
            'theme3' => 'Theme3',
            'theme4' => 'Theme4',
            'theme5' => 'Theme5',
            'theme6' => 'Theme6',
            'independent_work' => 'Independent Work',
            'final_test' => 'Final Test',
            'final_test_1st' => 'Final Test 1st',
            'final_test_2st' => 'Final Test 2st',
            'training_completed' => 'Training Completed',
            'number_children' => 'Number Children',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_ip' => 'Created Ip',
            'updated_ip' => 'Updated Ip',
        ];
    }
}
