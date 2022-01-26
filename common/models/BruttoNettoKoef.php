<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "brutto_netto_koef".
 *
 * @property int $id
 * @property int $products_category_id
 * @property int $koeff_netto
 * @property float $koeff_brutto
 * @property float $date_start
 * @property int $date_end
 * @property string $created_at
 */
class BruttoNettoKoef extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'brutto_netto_koef';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['products_category_id', 'products_id', 'koeff_netto', 'koeff_brutto', 'season'], 'required'],
            [['products_category_id', 'products_id', 'season'], 'integer'],
            [['koeff_brutto', 'koeff_netto'], 'number'],
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
            'products_category_id' => 'Категория продукта',
            'products_id' => 'Продукт',
            'season' => 'Сезон',
            'koeff_netto' => 'Коэффициент нетто(перевод из нетто в брутто)',
            'koeff_brutto' => 'Коэффициент брутто(перевод из брутто в нетто)',
            'created_at' => 'Дата загрузки в базу',
        ];
    }

    public function get_category($category_id){
        $category = ProductsCategory::findOne($category_id);
        return $category;
    }

    public function get_products($products_id){
        if($products_id == 0){
            return 'Все продукты';
        }
        $products = Products::findOne($products_id)->name;
        return $products;
    }

    public function get_date($date){
        $date = date('d.m.Y  H:i', strtotime($date));
        return $date;
    }
    public function get_season($season){
        if($season == 0){
            return 'Все';
        }
        if($season == 1){
            return 'Лето';
        }
        if($season == 2){
            return 'Осень';
        }
        if($season == 3){
            return 'Зима';
        }
        if($season == 4){
            return 'Весна';
        }
    }
}
