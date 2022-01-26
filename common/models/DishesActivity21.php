<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "dishes".
 *
 * @property int $id
 * @property string|null $name
 * @property int|null $dishes_category_id
 * @property int|null $recipes_collection_id
 * @property string|null $description
 * @property int|null $culinary_processing_id
 * @property int|null $yield
 * @property string|null $dishes_characters
 * @property string|null $techmup_number
 * @property int|null $number_of_dish
 * @property string|null $created_at
 */
class DishesActivity21 extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'dishes';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('db_activity21');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['dishes_category_id', 'recipes_collection_id', 'culinary_processing_id', 'yield', 'number_of_dish'], 'integer'],
            [['description', 'dishes_characters'], 'string'],
            [['created_at'], 'safe'],
            [['name', 'techmup_number'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'dishes_category_id' => 'Dishes Category ID',
            'recipes_collection_id' => 'Recipes Collection ID',
            'description' => 'Description',
            'culinary_processing_id' => 'Culinary Processing ID',
            'yield' => 'Yield',
            'dishes_characters' => 'Dishes Characters',
            'techmup_number' => 'Techmup Number',
            'number_of_dish' => 'Number Of Dish',
            'created_at' => 'Created At',
        ];
    }
}
