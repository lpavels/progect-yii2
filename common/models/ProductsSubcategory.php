<?php

namespace common\models;

use common\models\ProductsCategory;
use Yii;


/**
 * This is the model class for table "products_subcategory".
 *
 * В ТАБЛИЦЕ ХРАНЯТСЯ ПОДКАТЕГОРИИ КАТЕГОРИЙ ТАБЛИЦЫ ПРОДУКТОВ
 *
 * @property int $id
 * @property int $product_category_id
 * @property string $name
 * @property string $created_at
 */
class ProductsSubcategory extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'products_subcategory';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['product_category_id', 'name'], 'required'],
            [['product_category_id'], 'integer'],
            [['created_at'], 'safe'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'product_category_id' => 'Категория продукта',
            'name' => 'Подкатегория продукта',
            'created_at' => 'Дата добавления в базу',
        ];
    }

    public function get_category($category_id){
        $category = ProductsCategory::findOne($category_id);
        return $category;
    }
}
