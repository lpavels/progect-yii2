<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Dishes;


class DishesSearch extends Dishes
{

    public function rules()
    {
        return [
            //[['id', 'status'], 'integer'],
            [['name', 'dishes_category_id', 'recipes_collection_id', 'techmup_number'], 'safe'],
        ];
    }


    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }


    public function search($params)
    {
        if(Yii::$app->user->can('admin'))
        {
            $recipes_ids = [];
            $recipes_collection = RecipesCollection::find()->where(['organization_id' => [1, Yii::$app->session['organization_id']]])->all();
            foreach($recipes_collection as $r_collection){
                $recipes_ids[] = $r_collection->id;
            }

            $query = Dishes::find()->where(['recipes_collection_id' => $recipes_ids]);
        }
        /*elseif (Yii::$app->user->can('rospotrebnadzor_nutrition') || Yii::$app->user->can('rospotrebnadzor_camp')  || Yii::$app->user->can('subject_minobr')){
            $recipes_ids = [];
            $recipes_collection = RecipesCollection::find()->where(['organization_id' => [1, Yii::$app->session['organization_id']]])->all();
            foreach($recipes_collection as $r_collection){
                $recipes_ids[] = $r_collection->id;
            }
            $query = Dishes::find()->where(['recipes_collection_id' => $recipes_ids]);
        }*/
        elseif (Yii::$app->request->pathInfo == 'dishes/dishes-base'){
            $recipes_ids = [];
            $recipes_collection = RecipesCollection::find()->where(['organization_id' => [1, Yii::$app->user->identity->organization_id]])->all();
            foreach($recipes_collection as $r_collection){
                $recipes_ids[] = $r_collection->id;
            }
            $query = Dishes::find()->where(['recipes_collection_id' => $recipes_ids]);
        }
        elseif (Yii::$app->request->pathInfo == 'dishes' || Yii::$app->request->pathInfo == 'dishes/index'){
            $recipes_ids = [];
            $recipes_collection = RecipesCollection::find()->where(['organization_id' => [Yii::$app->user->identity->organization_id]])->all();
            foreach($recipes_collection as $r_collection){
                $recipes_ids[] = $r_collection->id;
            }
            $query = Dishes::find()->where(['recipes_collection_id' => $recipes_ids]);
        }

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
                //'pageSize' => 2
            ]
        ]);


        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['=', 'dishes_category_id', $this->dishes_category_id])
            ->andFilterWhere(['like', 'recipes_collection_id', $this->recipes_collection_id])
            ->andFilterWhere(['like', 'techmup_number', $this->techmup_number]);

        /*$query->andFilterWhere(['like', 'ugroup', $this->ugroup]);

        if(Yii::$app->user->can('admin')){
            $query->andFilterWhere(['=', 'city_id', $this->city_id]);

            $query->andFilterWhere(['like', 'ugroup', $this->ugroup]);
        }*/




        return $dataProvider;
    }
}
