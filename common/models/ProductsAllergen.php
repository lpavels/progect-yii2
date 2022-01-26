<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "products_allergen".
 *
 * @property int $id
 * @property int $products_id
 * @property int $allergen_id
 * @property string $created_at
 */
class ProductsAllergen extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'products_allergen';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['products_id', 'allergen_id'], 'required'],
            [['products_id', 'allergen_id'], 'integer'],
            [['created_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'products_id' => 'Продукт',
            'allergen_id' => 'Аллерген',
            'created_at' => 'Дата добавления в базу',
        ];
    }

    public function get_products($id){
        $dishes_products = Products::find()->where(['id'=>$id])->one();
        return $dishes_products;
    }

    public function get_allergen($id){
        $dishes_products = Allergen::find()->where(['id'=>$id])->one();
        return $dishes_products->name;
    }
}
