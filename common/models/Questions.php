<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "questions".
 *
 * @property int $id ID
 * @property int $training_program_id Тип обучающей программы
 * @property int $theme_questions_id Тема вопроса
 * @property string $name Вопрос
 * @property string $creat_at
 */
class Questions extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'questions';
    }

    public function rules()
    {
        return [
            [['training_program_id', 'theme_questions_id', 'name'], 'required'],
            [['training_program_id', 'theme_questions_id'], 'integer'],
            [['creat_at'], 'safe'],
            [['name'], 'string', 'max' => 400],
        ];
    }

    public function attributeLabels()
    {
        return [
            'Программы обучения' => 'ID',
            'training_program_id' => 'Training Program ID',
            'theme_questions_id' => 'Theme Questions ID',
            'name' => 'Name',
            'creat_at' => 'Creat At',
        ];
    }
}
