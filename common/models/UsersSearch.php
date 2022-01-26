<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;


class UsersSearch extends User
{
    public function rules()
    {
        return [
            [['id', 'key_login', 'organization_id', 'name', 'class', 'bukva_klassa', 'created_at'], 'safe'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        if (Yii::$app->user->can('admin'))
        {
            $query = User::find();
        }
//        elseif (Yii::$app->user->can('RPN'))
//        {
//            $query = User::find()->where(['key_login_rpn' => User::findOne(Yii::$app->user->id)->key_login]);
//        }

        $this->load($params);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 50
            ]
        ]);

//        if ($this->district_id == '' && $this->key_login_departament == ''){return $dataProvider;}
//
//        $query->andFilterWhere(['district_id' => $this->district_id])
//            ->andFilterWhere(['region_id' => $this->region_id])
//            ->andFilterWhere(['municipality_id' => $this->municipality_id])
//            ->andFilterWhere(['like', 'key_login_departament', $this->key_login_departament]);

        return $dataProvider;
    }
}
