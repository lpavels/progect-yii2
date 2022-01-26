<?php

namespace common\models;

use common\models\Organization;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;


class OrganizationSearch extends Organization
{

    public function rules()
    {
        return [
            //[['id', 'federal_district_id'], 'integer'],
            [['title', 'federal_district_id', 'region_id', 'municipality_id', 'short_title', 'type_org'], 'safe'],
        ];
    }


    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }


    public function search($params)
    {
        $query = Organization::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate())
        {
            return $dataProvider;
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                //'forcePageParam' => false,
                //'pageSizeParam' => false,
                'pageSize' => 50
            ]
        ]);

        if (Yii::$app->user->can('admin'))
        {
            $query->andFilterWhere(['like', 'title', $this->title])
                ->andFilterWhere(['like', 'short_title', $this->short_title])
                ->andFilterWhere(['=', 'federal_district_id', $this->federal_district_id])
                ->andFilterWhere(['=', 'region_id', $this->region_id])
                ->andFilterWhere(['=', 'municipality_id', $this->municipality_id])
                ->andFilterWhere(['=', 'type_org', $this->type_org]);
        }
        elseif (Yii::$app->user->can('RPN'))
        {
            $query->andFilterWhere(['like', 'title', $this->title])
                ->andFilterWhere(['like', 'short_title', $this->short_title])
                ->andFilterWhere(['=', 'region_id', DepartmentEducation::findOne(['key_login_rpn'=>Yii::$app->user->identity->key_login])->region_id])
                ->andFilterWhere(['=', 'region_id', DepartmentEducation::findOne(['key_login_ministry_education'=>Yii::$app->user->identity->key_login])->region_id])
                ->andFilterWhere(['=', 'municipality_id', $this->municipality_id])
                ->andFilterWhere(['=', 'type_org', $this->type_org]);
        }
        elseif (Yii::$app->user->can('RPN_mun'))
        {
            $query->andFilterWhere(['like', 'title', $this->title])
                ->andFilterWhere(['like', 'short_title', $this->short_title])
                ->andFilterWhere(['=', 'municipality_id', DepartmentEducation::findOne(['key_login_departament'=>Yii::$app->user->identity->key_login])->municipality_id])
                ->andFilterWhere(['=', 'municipality_id', DepartmentEducation::findOne(['key_login_ministry_education'=>Yii::$app->user->identity->key_login])->municipality_id])
                ->andFilterWhere(['=', 'type_org', $this->type_org]);
        }


        /* $query->andFilterWhere(['like', 'name', $this->name])
             ->andFilterWhere(['=', 'products_category_id', $this->products_category_id])
             ->andFilterWhere(['=', 'products_subcategory_id', $this->products_subcategory_id]);*/

        /*$query->andFilterWhere(['like', 'ugroup', $this->ugroup]);

        if(Yii::$app->user->can('admin')){
            $query->andFilterWhere(['=', 'city_id', $this->city_id]);

            $query->andFilterWhere(['like', 'ugroup', $this->ugroup]);
        }*/

        return $dataProvider;
    }
}
