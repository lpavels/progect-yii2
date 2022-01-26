<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "dishes_products".
 *
 * @property int $id
 * @property int $dishes_id
 * @property int $products_id
 * @property float $net_weight
 * @property float $gross_weight
 * @property string $created_at
 */
class DishesProducts extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'dishes_products';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['dishes_id', 'products_id', 'net_weight', 'gross_weight'], 'required'],
            [['dishes_id'], 'integer'],
            [['net_weight', 'gross_weight'], 'number'],
            [['created_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'dishes_id' => 'Блюдо',
            'products_id' => 'Продукт',
            'net_weight' => 'Вес нетто',
            'gross_weight' => 'Вес брутто',
            'created_at' => 'Дата добавления',
        ];
    }

    public function get_products($id){
        $products = Products::findOne($id);
        return $products;
    }
    /*РАСЧЕТ БЖУ С КОЕФИЦИЕНТАМИ ИСПОЛЬЗУЕТСЯ В ТЕХКАРТАХ*/
    public function get_products_bju($id, $dishes_id, $field){
        if($field == 'protein'){
            $koef = 0.94;
        }
        elseif ($field == 'fat'){
            $koef = 0.88;
        }
        elseif($field == 'carbohydrates_total'){
            $koef = 0.91;
        }
        $culinary_processing = Dishes::findOne($dishes_id)->culinary_processing_id;
        if($culinary_processing != 3){
            $products = Products::findOne($id)->$field * $koef;
        }
        else{
            $products = Products::findOne($id)->$field;
        }

        return $products;
    }

    public function get_total_products($id, $field){
        $dishes_products = DishesProducts::find()->where(['dishes_id'=>$id])->all();
        $total = 0;
        foreach($dishes_products as $d_product){
            $product = $this->get_products($d_product->products_id)->$field;
            $total = $total +$product;
        }
        return $total;
    }

    public function get_count_products($id){
        $dishes_products = DishesProducts::find()->where(['dishes_id'=>$id])->count();
        return $dishes_products;
    }

    public function get_date($date){
        $date = date('d.m.Y  H:i', strtotime($date));
        return $date;
    }

    public function get_dishes($id){
        $d = Dishes::findOne($id);
        return $d->name;
    }

    public function get_product($id){
        $d = Products::findOne($id);
        return $d->name;
    }

    public function get_category($id){
        $d = ProductsCategory::findOne($id);
        return $d->name;
    }
    public function get_menus($id){
        $d = Menus::findOne($id);
        return $d->name;
    }

    public function get_kkal($id, $dishes_id){
        $culinary_processing = Dishes::findOne($dishes_id)->culinary_processing_id;
        $product = Products::findOne($id);
        if($culinary_processing != 3){
            $kkal = ($product->protein * 4 * 0.94) + ($product->fat * 9 * 0.88) + ($product->carbohydrates_total * 4 * 0.91);
        }
        else{
            $kkal = ($product->protein * 4) + ($product->fat * 9) + ($product->carbohydrates_total * 4);
        }
        return $kkal;
    }



    public function get_yield($dishes_id){

        $dishes = Dishes::findOne($dishes_id);
        return $dishes->yield;
    }


    public function get_vitamin($products_id, $dishes_id, $field)
    {
        $dish = Dishes::findOne($dishes_id);

        $product = Products::findOne($products_id);
        $uvarka = 1;


        if ($dish->culinary_processing_id != 3)
        {
            if ($field == 'vitamin_a')
            {
                $uvarka = 0.6;
            }
            elseif ($field == 'vitamin_b1')
            {
                $uvarka = 0.72;
            }
            elseif ($field == 'vitamin_c')
            {
                $uvarka = 0.40;
            }
            elseif ($field == 'vitamin_pp' || $field == 'vitamin_b2' || $field == 'vitamin_b_carotene')
            {
                $uvarka = 0.8;
            }
            elseif ($field == 'mg' || $field == 'p' || $field == 'fe')
            {
                $uvarka = 0.87;
            }
            elseif ($field == 'ca' || $field == 'se')
            {
                $uvarka = 0.88;
            }
            elseif($field == 'na'){
                $uvarka = 0.76;
            }
            elseif($field == 'k'){
                $uvarka = 0.83;
            }
            else
            {
                $uvarka = 1;
            }
        }

        $protein = ($product->$field * $uvarka);
        return $protein;
    }
}
