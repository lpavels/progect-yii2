<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "questions_response".
 *
 * @property int $id ID
 * @property int $user_id Пользователь
 * @property int $questions_id Аопрос
 * @property int $questions_variant_id Вопрос
 * @property string $creat_at
 */
class QuestionsResponse extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'questions_response';
    }

    public function rules()
    {
        return [
            [['user_id', 'questions_id', 'questions_variant_id', 'number_trying'], 'required'],
            [['user_id', 'questions_id', 'questions_variant_id', 'number_trying'], 'integer'],
            [['creat_at'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'questions_id' => 'Questions ID',
            'questions_variant_id' => 'Questions Variant ID',
            'creat_at' => 'Creat At',
        ];
    }
}
