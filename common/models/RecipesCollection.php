<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "recipes_collection".
 *
 * @property int $id
 * @property string $name
 * @property int $year
 * @property string $created_at
 */
class RecipesCollection extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'recipes_collection';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'short_title', 'year',], 'required'],
            [['year', 'organization_id'], 'integer'],
            [['created_at'], 'safe'],
            [['name', 'short_title'], 'string', 'max' => 255],
        ];
    }



    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'short_title' => 'Короткое название',
            'year' => 'Год сборника',
            'organization_id' => 'Организация',
            'created_at' => 'Дата добавления',
        ];
    }

    public function beforeSave($insert){
        if (parent::beforeSave($insert)) {
            $this->organization_id = Yii::$app->user->identity->organization_id;
            return true;
        }
        return false;
    }

    public function get_count_dishes($id){
        $category = Dishes::find()->where(['recipes_collection_id' => $id])->count();
        return $category;
    }
    public function get_date($date){
        $date = date('d.m.Y  H:i', strtotime($date));
        return $date;
    }

    public function get_organization($id){
        $category = Organization::find()->where(['id' => $id])->one();
        return $category->title;
    }
}
