<?php

namespace common\models;

use Yii;

class Anket extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'anket';
    }

    public function rules()
    {
        if (Yii::$app->user->can('school1011')||Yii::$app->user->can('school14')||Yii::$app->user->can('school511')||Yii::$app->user->can('school56')||Yii::$app->user->can('school59')||Yii::$app->user->can('school79'))
        {
            $return [] = [['informativity', 'utility', 'visualization', 'class_chas', 'parent_meet'], 'required'];
            $return [] = [['class_chas_date'], 'required', 'when' => function ($model2){return $model2->class_chas == '1';},
                'whenClient' => "function (attribute, value) {return $('#anket-class_chas').val() == '1';}"];
            $return [] = [['parent_meet_date'], 'required', 'when' => function ($model2){return $model2->parent_meet == '1';},
                'whenClient' => "function (attribute, value) {return $('#anket-parent_meet').val() == '1';}"];
        }
        else
        {
            $return[] = [['informativity','utility','visualization'], 'required'];
        }
        return $return;
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'informativity' => '1.1 Информативность программы',
            'informativity_comment' => '1.2 Ваши предложения по повышению информативности материалов',
            'utility' => '1.3 Полезность программы',
            'utility_comment' => '1.4 Ваши предложения по повышению полезности материалов',
            'visualization' => '1.5 Наглядность программы',
            'visualization_comment' => '1.6 Ваши предложения по повышению наглядности материалов',

            'class_chas' => 'Классный час',
            'class_chas_date' => 'Дата',
            'parent_meet' => 'Родительское собрание',
            'parent_meet_date' => 'Дата',
            'inoe' => 'Иное',
        ];
    }

    public function select($id = false)
    {
        $select_item = ['Нет', 'Да, информативно'];
        if (!is_bool($id))
        {
            return $select_item[$id];
        }
        else
        {
            return $select_item;
        }
    }

    /*public function sex($id = false)
    {
        $sex_item = ['мужской','женский'];
        if (!is_bool($id))
        {
            return $sex_item[$id];
        }
        else  {
            return $sex_item;
        }
    }*/
}
