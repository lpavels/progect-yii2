<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "questions_variant".
 *
 * @property int $id ID
 * @property int $questions_id Вопрос
 * @property int $type_questions Вопрос
 * @property string $name название
 * @property int $correct 1- это правильный 0 - не правильный
 * @property string $creat_at
 */
class QuestionsVariant extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'questions_variant';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['questions_id', 'type_questions', 'name', 'correct'], 'required'],
            [['questions_id', 'type_questions', 'correct'], 'integer'],
            [['creat_at'], 'safe'],
            [['name'], 'string', 'max' => 400],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'questions_id' => 'Questions ID',
            'type_questions' => 'Type Questions',
            'name' => 'Name',
            'correct' => 'Correct',
            'creat_at' => 'Creat At',
        ];
    }
}
