<?php

namespace common\models;

use Yii;
use yii\base\Model;

class QuestionsForm extends Model
{
    public $question1;
    public $question2;
    public $question3;
    public $question4;
    public $question5;
    public $question6;
    public $question7;
    public $question8;
    public $question9;
    public $question10;
    public $question11;
    public $question12;
    public $question13;
    public $question14;
    public $question15;
    public $question16;
    public $question17;
    public $question18;
    public $question19;
    public $question20;
    public $question21;
    public $question22;
    public $question23;
    public $question24;
    public $question25;
    public $question26;
    public $question27;
    public $question28;
    public $question29;
    public $question30;
    public $question31;
    public $question32;
    public $question33;
    public $question34;
    public $question35;
    public $question36;
    public $question37;
    public $question38;
    public $question39;

    public function rules()
    {
        return [
            [['question1', 'question2', 'question3', 'question4', 'question5', 'question6', 'question7', 'question8', 'question9', 'question10'], 'required'],
        ];
    }

    public function attributeLabels()
    {
        return [

        ];
    }

    public function ball_response($id_u)
    {
        //$id_u - номер пользователя
        //$questions - количество вопросов всего
        $questions = QuestionsResponse::find()->where(['user_id' => $id_u, 'status' => 1])->all();
        $caunt = 0;
        foreach ($questions as $question)
        {
            $variant = QuestionsVariant::find()->where(['id' => $question->questions_variant_id])->one();
            if ($variant->correct == '1')
            {
                $caunt++;
            }
        }
        return $caunt;
    }
}
