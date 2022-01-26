<?php

namespace common\models;

use Yii;
use common\models\RecipesCollection;
use common\models\DishesCategory;


/**
 * This is the model class for table "dishes".
 *
 * @property int $id
 * @property string $name
 * @property int $dishes_category_id
 * @property int $recipes_collection_id
 * @property string $description
 * @property int $culinary_processing_id
 * @property int $yield
 * @property string $techmup_number
 * @property int $number_of_dish
 * @property string $created_at
 */
class Dishes extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'dishes';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'dishes_category_id', 'recipes_collection_id', 'description', 'culinary_processing_id', 'yield'], 'required'],
            [['dishes_category_id', 'recipes_collection_id', 'culinary_processing_id', 'yield'], 'integer'],
            [['description', 'dishes_characters'], 'string'],
            [['created_at', 'dishes_characters'], 'safe'],
            [['name', 'techmup_number', 'number_of_dish'], 'string', 'max' => 255],
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
            'dishes_category_id' => 'Категория блюда',
            'recipes_collection_id' => 'Сборник',
            'description' => 'Описание',
            'culinary_processing_id' => 'Процесс приготовления',
            'yield' => 'Выход в граммах',
            'dishes_characters' => 'Характеристика блюда на выходе',
            'techmup_number' => 'Номер технологической карты',
            'number_of_dish' => 'Сборник источник(наименование оригинального сборника)',
            'created_at' => 'Дата добавления в базу',
        ];
    }

    public function get_recipes($id)
    {
        $recipes = RecipesCollection::findOne($id);
        return $recipes;
    }

    public function get_count_menus($id)
    {
        $dishes = MenusDishes::find()->where(['dishes_id' => $id])->count();
        return $dishes;
    }

    public function get_dishes($id)
    {
        $dishes = Dishes::findOne($id);
        return $dishes->name;
    }

    public function get_culinary_processing($id)
    {
        $c = CulinaryProcessing::findOne($id);
        return $c->name;
    }

    public function get_category_dish($id)
    {
        $category = DishesCategory::findOne($id);
        return $category->name;
    }

    public function get_recipes_collection($id){
        $recipes = RecipesCollection::findOne($id);
        return $recipes;
    }

    public function get_count_products($id){
        $dishes_products = DishesProducts::find()->where(['dishes_id'=>$id])->count();
        return $dishes_products;
    }


}
