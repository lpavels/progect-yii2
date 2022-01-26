<?php

namespace common\models;

use common\models\Organization;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;


class MenuSearch extends Menus
{

    public function rules()
    {
        return [
            //[['id', 'federal_district_id'], 'integer'],
            [['name', 'feeders_characters_id', 'age_info_id'], 'safe'],
        ];
    }


    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }


    public function search($params)
    {
        $query = Menus::find()->where(['organization_id' => Yii::$app->user->identity->organization_id, 'status_archive' => 1]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                //'forcePageParam' => false,
                //'pageSizeParam' => false,
                'pageSize' => 20
            ]
        ]);


        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['=', 'feeders_characters_id', $this->feeders_characters_id])
            ->andFilterWhere(['=', 'age_info_id', $this->age_info_id]);

        return $dataProvider;
    }
}
