<?php

namespace common\models;

use Yii;
use yii\base\Model;

class Recalculation extends Model
{
    public function ball_response_start($id_u)
    {
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

    public function ball_response_end($id_u)
    {
        $numberTrying = QuestionsResponse::find()->where(['user_id' => $id_u, 'status' => 2])->orderBy(['number_trying'=>SORT_DESC])->one()->number_trying;
        $questions = QuestionsResponse::findAll(['user_id' => $id_u, 'status' => 2, 'number_trying' => $numberTrying]);

        $count = 0;
        foreach ($questions as $question)
        {
            $variant = QuestionsVariant::find()->where(['id' => $question->questions_variant_id])->one();
            if ($variant->correct == '1')
            {
                $count++;
            }
        }
        return array($numberTrying,$count);
    }
}
