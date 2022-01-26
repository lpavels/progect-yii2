<?php

namespace common\models;


use common\models\ProductsCategory;
use common\models\ProductsSubcategory;
use Yii;


/**
 * This is the model class for table "products".
 *
 * @property int $id
 * @property string $name
 * @property int $products_category_id
 * @property int $products_subcategory_id
 * @property float $water
 * @property float $protein
 * @property float $fat
 * @property float $carbohydrates_total
 * @property float $carbohydrates_saccharide
 * @property float $carbohydrates_starch
 * @property float $carbohydrates_lactose
 * @property float $carbohydrates_sacchorose
 * @property float $carbohydrates_cellulose
 * @property float $dust_total
 * @property float $dust_nacl
 * @property float $apple_acid
 * @property float $na
 * @property float $k
 * @property float $ca
 * @property float $mg
 * @property float $p
 * @property float $fe
 * @property float $i
 * @property float $se
 * @property float $f
 * @property float $vitamin_a
 * @property float $vitamin_b_carotene
 * @property float $vitamin_b1
 * @property float $vitamin_b2
 * @property float $vitamin_pp
 * @property float $vitamin_c
 * @property float $vitamin_d
 * @property float $energy_kkal
 * @property float $energy_kdj
 * @property string $created_at
 */
class Products extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'products';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'products_category_id', 'products_subcategory_id', 'water', 'protein', 'fat', 'carbohydrates_total', 'carbohydrates_saccharide', 'carbohydrates_starch', 'carbohydrates_lactose', 'carbohydrates_sacchorose', 'carbohydrates_cellulose', 'dust_total', 'dust_nacl', 'apple_acid', 'na', 'k', 'ca', 'mg', 'p', 'fe', 'i', 'se', 'f', 'vitamin_a', 'vitamin_b_carotene', 'vitamin_b1', 'vitamin_b2', 'vitamin_pp', 'vitamin_c', 'vitamin_d', 'energy_kkal', 'energy_kdj', 'sort'], 'required'],
            [['products_category_id', 'products_subcategory_id', 'sort'], 'integer'],
            [['water', 'protein', 'fat', 'carbohydrates_total', 'carbohydrates_saccharide', 'carbohydrates_starch', 'carbohydrates_lactose', 'carbohydrates_sacchorose', 'carbohydrates_cellulose', 'dust_total', 'dust_nacl', 'apple_acid', 'na', 'k', 'ca', 'mg', 'p', 'fe', 'i', 'se', 'f', 'vitamin_a', 'vitamin_b_carotene', 'vitamin_b1', 'vitamin_b2', 'vitamin_pp', 'vitamin_c', 'vitamin_d', 'energy_kkal', 'energy_kdj'], 'number'],
            [['created_at'], 'safe'],
            [['name'], 'string', 'max' => 255],
            //['name', 'validateEmail'],
        ];
    }


    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название, г',
            'products_category_id' => 'Категория продукта',
            'products_subcategory_id' => 'Подкатегория продукта',
            'sort' => 'Приоритет',
            'water' => 'Вода, г',
            'protein' => 'Белки, г',
            'fat' => 'Жиры, г',
            'carbohydrates_total' => 'Углеводы(общие), г',
            'carbohydrates_saccharide' => 'Моно- и Дисахариды, г',
            'carbohydrates_starch' => 'Крахмал, г',
            'carbohydrates_lactose' => 'Лактоза, г',
            'carbohydrates_sacchorose' => 'Сахароза, г',
            'carbohydrates_cellulose' => 'Пищевые волокна, г',
            'dust_total' => 'Зола(общая), г',
            'dust_nacl' => 'Зола(в т.ч. добавл. NaCl), г',
            'apple_acid' => 'Орг.кислоты в пересчете на яблочную, г',
            'na' => 'Натрий, мг',
            'k' => 'Калий, мг',
            'ca' => 'Кальций, мг',
            'mg' => 'Магний, мг',
            'p' => 'Фосфор, мг',
            'fe' => 'Железо, мг',
            'i' => 'Йод, мкг',
            'se' => 'Селен, мкг',
            'f' => 'Фтор, мкг',
            'vitamin_a' => 'Витамин А, мкг рет. экв.',
            'vitamin_b_carotene' => 'Витамин B-каротин, мг',
            'vitamin_b1' => 'Витамин B1, мг',
            'vitamin_b2' => 'Витамин B2, мг',
            'vitamin_pp' => 'Витамин PР, мг',
            'vitamin_c' => 'Витамин C, мг',
            'vitamin_d' => 'Витамин D, мкг',
            'energy_kkal' => 'Эн. ценность, Ккал',
            'energy_kdj' => 'Эн. ценность, кДж',
            'created_at' => 'Создано',
        ];
    }
    public function get_category($category_id){
        $category = ProductsCategory::findOne($category_id);
        return $category;
    }

    public function get_allergen($id){
        $p = ProductsAllergen::find()->where(['products_id' => $id])->count();
        if($p > 0){
            return 'Есть';

        }
        else{
            return 'Не добавлен';
        }
    }
    public function get_subcategory($category_id){
        $category = ProductsSubcategory::findOne($category_id);
        return $category;
    }

    public function get_total_yield_day($product_id, $menu_id, $cycle, $days_id, $brutto_or_netto){
        $result = [];

        $menus_dishes = MenusDishes::find()->where(['date_fact_menu' => 0, 'menu_id' => $menu_id, 'cycle' => $cycle, 'days_id'=> $days_id])->all();
        $sum = 0;
        if($brutto_or_netto == 0){
            $field = 'gross_weight';
        }
        elseif($brutto_or_netto == 1){
            $field = 'net_weight';
        }
        else{
            return 'ошибка';
        }

        foreach($menus_dishes as $m_dish){
            $products = DishesProducts::find()->where(['dishes_id' => $m_dish->dishes_id, 'products_id' => $product_id])->all();
            $dish = Dishes::findOne($m_dish->dishes_id);
            $dish_yield = $dish->yield;

            foreach ($products as $product){
                $prod = Products::findOne($product->products_id);
                $sum = $product->$field * $m_dish->yield/$dish_yield + $sum;
                //Если были замены продуктов, то активировать замену продукта
                $products_change = ProductsChangeOrganization::find()->where(['organization_id' => Yii::$app->user->identity->organization_id, 'products_id' => $product->products_id])->one();
                if(!empty($products_change)){
                    $prod = Products::findOne($products_change->change_products_id);
                }


                if ($dish->culinary_processing_id != 3){
                    $result['vitamin_a'] = (0.6*($prod->vitamin_a * $product->net_weight)*($m_dish->yield/$dish_yield)/100) + $result['vitamin_a'];
                    $result['vitamin_b1'] = (0.72*($prod->vitamin_b1 * $product->net_weight)*($m_dish->yield/$dish_yield)/100) + $result['vitamin_b1'];
                    $result['vitamin_c'] = (0.40*($prod->vitamin_c * $product->net_weight)*($m_dish->yield/$dish_yield)/100) + $result['vitamin_c'];

                    $result['vitamin_pp'] = (0.8*($prod->vitamin_pp * $product->net_weight)*($m_dish->yield/$dish_yield)/100) + $result['vitamin_pp'];
                    $result['vitamin_b2'] = (0.8*($prod->vitamin_b2 * $product->net_weight)*($m_dish->yield/$dish_yield)/100) + $result['vitamin_b2'];
                    $result['vitamin_b_carotene'] = (0.8*($prod->vitamin_b_carotene * $product->net_weight)*($m_dish->yield/$dish_yield)/100) + $result['vitamin_b_carotene'];

                    $result['mg'] = (0.87*($prod->mg * $product->net_weight)*($m_dish->yield/$dish_yield)/100) + $result['mg'];
                    $result['p'] = (0.87*($prod->p * $product->net_weight)*($m_dish->yield/$dish_yield)/100) + $result['p'];
                    $result['fe'] = (0.87*($prod->fe * $product->net_weight)*($m_dish->yield/$dish_yield)/100) + $result['fe'];

                    $result['ca'] = (0.88*($prod->ca * $product->net_weight)*($m_dish->yield/$dish_yield)/100) + $result['ca'];
                    $result['se'] = (0.88*($prod->se * $product->net_weight)*($m_dish->yield/$dish_yield)/100) + $result['se'];
                    $result['na'] = (0.76*($prod->na*$product->net_weight)*($m_dish->yield/$dish_yield)/100) + $result['na'];
                    $result['k'] = (0.83*($prod->k * $product->net_weight)*($m_dish->yield/$dish_yield)/100) + $result['k'];
                    $result['vitamin_d'] = (1*($prod->vitamin_d * $product->net_weight)*($m_dish->yield/$dish_yield)/100) + $result['vitamin_d'];
                    $result['f'] = (1*($prod->f * $product->net_weight)*($m_dish->yield/$dish_yield)/100) + $result['f'];
                    $result['i'] = (1*($prod->i * $product->net_weight)*($m_dish->yield/$dish_yield)/100) + $result['i'];
                }else{

                    $result['vitamin_a'] = (1*($prod->vitamin_a * $product->net_weight)*($m_dish->yield/$dish_yield)/100) + $result['vitamin_a'];
                    $result['vitamin_b1'] = (1*($prod->vitamin_b1 * $product->net_weight)*($m_dish->yield/$dish_yield)/100) + $result['vitamin_b1'];
                    $result['vitamin_c'] = (1*($prod->vitamin_c * $product->net_weight)*($m_dish->yield/$dish_yield)/100) + $result['vitamin_c'];

                    $result['vitamin_pp'] = (1*($prod->vitamin_pp * $product->net_weight)*($m_dish->yield/$dish_yield)/100) + $result['vitamin_pp'];
                    $result['vitamin_b2'] = (1*($prod->vitamin_b2 * $product->net_weight)*($m_dish->yield/$dish_yield)/100) + $result['vitamin_b2'];
                    $result['vitamin_b_carotene'] = (1*($prod->vitamin_b_carotene * $product->net_weight)*($m_dish->yield/$dish_yield)/100) + $result['vitamin_b_carotene'];

                    $result['mg'] = (1*($prod->mg * $product->net_weight)*($m_dish->yield/$dish_yield)/100) + $result['mg'];
                    $result['p'] = (1*($prod->p * $product->net_weight)*($m_dish->yield/$dish_yield)/100) + $result['p'];
                    $result['fe'] = (1*($prod->fe * $product->net_weight)*($m_dish->yield/$dish_yield)/100) + $result['fe'];

                    $result['ca'] = (1*($prod->ca * $product->net_weight)*($m_dish->yield/$dish_yield)/100) + $result['ca'];
                    $result['se'] = (1*($prod->se * $product->net_weight)*($m_dish->yield/$dish_yield)/100) + $result['se'];
                    $result['na'] = (1*($prod->na*$product->net_weight)*($m_dish->yield/$dish_yield)/100) + $result['na'];

                    $result['k'] = (1*($prod->k * $product->net_weight)*($m_dish->yield/$dish_yield)/100) + $result['k'];
                    $result['vitamin_d'] = (1*($prod->vitamin_d * $product->net_weight)*($m_dish->yield/$dish_yield)/100) + $result['vitamin_d'];
                    $result['f'] = (1*($prod->f * $product->net_weight)*($m_dish->yield/$dish_yield)/100) + $result['f'];
                    $result['i'] = (1*($prod->i * $product->net_weight)*($m_dish->yield/$dish_yield)/100) + $result['i'];
                }

            }
        }
        if($sum == 0){
            $result['yield'] = '-';
            return $result;
        }
        //для продуктов в малой концентрации округление должно быть до 2х знаков.
        if($product_id == 8 || $product_id == 218 || $product_id == 10 || $product_id == 240 || $product_id == 11){
            $result['yield'] = round($sum, 2);
            return  $result;
        }

        $result['yield'] = round($sum, 2);
        return  $result;


    }

    //ФУНКЦИЯ ДЛЯ ПЕРЕОДИЧНОЙ ВЕДОМОСТИ
    public function get_total_yield_day_period($menu_id, $product_id, $array, $brutto_or_netto){
        if(date("w", strtotime($array[0])) == 0){
            $day_name = 7;
        }else{
            $day_name = date("w", strtotime($array[0]));
        }
        //print_r($array);exit;
        $menus_dishes = MenusDishes::find()->where(['date_fact_menu' => strtotime($array[0]), 'menu_id' => $menu_id])->all();
        if(empty($menus_dishes)){
            $menus_dishes = MenusDishes::find()->where(['date_fact_menu' => 0, 'menu_id' => $menu_id, 'cycle' => $array[1], 'days_id'=> $day_name])->all();
        }
        //print_r($menu_id);exit;
        $sum = 0;
        if($brutto_or_netto == 0){
            $field = 'gross_weight';
        }
        elseif($brutto_or_netto == 1){
            $field = 'net_weight';
        }
        else{
            return 'ошибка';
        }

        foreach($menus_dishes as $m_dish){
            $products = DishesProducts::find()->where(['dishes_id' => $m_dish->dishes_id, 'products_id' => $product_id])->all();
            $dish_yield = Dishes::findOne($m_dish->dishes_id)->yield;

            foreach ($products as $product){
                $sum = $product->$field * $m_dish->yield/$dish_yield/* * ($m_dish->yield/100) )*/ + $sum;
            }
        }
        if($sum == 0){
            return '-';
        }
        //для продуктов в малой концентрации округление должно быть до 2х знаков.
        if($product_id == 8 || $product_id == 218 || $product_id == 10 || $product_id == 240 || $product_id == 11){
            return round($sum, 2);
        }

        return round($sum, 1);


    }

	public function get_total_yield_nutrition($product_id, $menu_id, $cycle, $days_id, $nutrition_id, $brutto_or_netto){
        $result = [];
        $menus_dishes = MenusDishes::find()->where(['date_fact_menu' => 0, 'menu_id' => $menu_id, 'cycle' => $cycle, 'days_id'=> $days_id, 'nutrition_id' => $nutrition_id])->all();
        $sum = 0;
        if($brutto_or_netto == 0){
            $field = 'gross_weight';
        }
        elseif($brutto_or_netto == 1){
            $field = 'net_weight';
        }
        else{
            return 'ошибка';
        }

        foreach($menus_dishes as $m_dish){
            $products = DishesProducts::find()->where(['dishes_id' => $m_dish->dishes_id, 'products_id' => $product_id])->all();
            $products_c = DishesProducts::find()->where(['dishes_id' => $m_dish->dishes_id, 'products_id' => $product_id])->count();
            $dish = Dishes::findOne($m_dish->dishes_id);
            $dish_yield = $dish->yield;

            foreach ($products as $product){
                $prod = Products::findOne($product->products_id);
                $sum = $product->$field * $m_dish->yield/$dish_yield/* * ($m_dish->yield/100) )*/ + $sum;
                //Если были замены продуктов, то активировать замену продукта
                $products_change = ProductsChangeOrganization::find()->where(['organization_id' => Yii::$app->user->identity->organization_id, 'products_id' => $product->products_id])->one();
                if(!empty($products_change)){
                    $prod = Products::findOne($products_change->change_products_id);
                }

                if ($dish->culinary_processing_id != 3){
                    $result['vitamin_a'] = (0.6*($prod->vitamin_a * $product->net_weight)*($m_dish->yield/$dish_yield)/100) + $result['vitamin_a'];
                    $result['vitamin_b1'] = (0.72*($prod->vitamin_b1 * $product->net_weight)*($m_dish->yield/$dish_yield)/100) + $result['vitamin_b1'];
                    $result['vitamin_c'] = (0.40*($prod->vitamin_c * $product->net_weight)*($m_dish->yield/$dish_yield)/100) + $result['vitamin_c'];

                    $result['vitamin_pp'] = (0.8*($prod->vitamin_pp * $product->net_weight)*($m_dish->yield/$dish_yield)/100) + $result['vitamin_pp'];
                    $result['vitamin_b2'] = (0.8*($prod->vitamin_b2 * $product->net_weight)*($m_dish->yield/$dish_yield)/100) + $result['vitamin_b2'];
                    $result['vitamin_b_carotene'] = (0.8*($prod->vitamin_b_carotene * $product->net_weight)*($m_dish->yield/$dish_yield)/100) + $result['vitamin_b_carotene'];

                    $result['mg'] = (0.87*($prod->mg * $product->net_weight)*($m_dish->yield/$dish_yield)/100) + $result['mg'];
                    $result['p'] = (0.87*($prod->p * $product->net_weight)*($m_dish->yield/$dish_yield)/100) + $result['p'];
                    $result['fe'] = (0.87*($prod->fe * $product->net_weight)*($m_dish->yield/$dish_yield)/100) + $result['fe'];

                    $result['ca'] = (0.88*($prod->ca * $product->net_weight)*($m_dish->yield/$dish_yield)/100) + $result['ca'];
                    $result['se'] = (0.88*($prod->se * $product->net_weight)*($m_dish->yield/$dish_yield)/100) + $result['se'];
                    $result['na'] = (0.76*($prod->na*$product->net_weight)*($m_dish->yield/$dish_yield)/100) + $result['na'];
                    $result['k'] = (0.83*($prod->k * $product->net_weight)*($m_dish->yield/$dish_yield)/100) + $result['k'];
                    $result['vitamin_d'] = (1*($prod->vitamin_d * $product->net_weight)*($m_dish->yield/$dish_yield)/100) + $result['vitamin_d'];
                    $result['f'] = (1*($prod->f * $product->net_weight)*($m_dish->yield/$dish_yield)/100) + $result['f'];
                    $result['i'] = (1*($prod->i * $product->net_weight)*($m_dish->yield/$dish_yield)/100) + $result['i'];
                }else{

                    $result['vitamin_a'] = (1*($prod->vitamin_a * $product->net_weight)*($m_dish->yield/$dish_yield)/100) + $result['vitamin_a'];
                    $result['vitamin_b1'] = (1*($prod->vitamin_b1 * $product->net_weight)*($m_dish->yield/$dish_yield)/100) + $result['vitamin_b1'];
                    $result['vitamin_c'] = (1*($prod->vitamin_c * $product->net_weight)*($m_dish->yield/$dish_yield)/100) + $result['vitamin_c'];

                    $result['vitamin_pp'] = (1*($prod->vitamin_pp * $product->net_weight)*($m_dish->yield/$dish_yield)/100) + $result['vitamin_pp'];
                    $result['vitamin_b2'] = (1*($prod->vitamin_b2 * $product->net_weight)*($m_dish->yield/$dish_yield)/100) + $result['vitamin_b2'];
                    $result['vitamin_b_carotene'] = (1*($prod->vitamin_b_carotene * $product->net_weight)*($m_dish->yield/$dish_yield)/100) + $result['vitamin_b_carotene'];

                    $result['mg'] = (1*($prod->mg * $product->net_weight)*($m_dish->yield/$dish_yield)/100) + $result['mg'];
                    $result['p'] = (1*($prod->p * $product->net_weight)*($m_dish->yield/$dish_yield)/100) + $result['p'];
                    $result['fe'] = (1*($prod->fe * $product->net_weight)*($m_dish->yield/$dish_yield)/100) + $result['fe'];

                    $result['ca'] = (1*($prod->ca * $product->net_weight)*($m_dish->yield/$dish_yield)/100) + $result['ca'];
                    $result['se'] = (1*($prod->se * $product->net_weight)*($m_dish->yield/$dish_yield)/100) + $result['se'];
                    $result['na'] = (1*($prod->na*$product->net_weight)*($m_dish->yield/$dish_yield)/100) + $result['na'];

                    $result['k'] = (1*($prod->k * $product->net_weight)*($m_dish->yield/$dish_yield)/100) + $result['k'];
                    $result['vitamin_d'] = (1*($prod->vitamin_d * $product->net_weight)*($m_dish->yield/$dish_yield)/100) + $result['vitamin_d'];
                    $result['f'] = (1*($prod->f * $product->net_weight)*($m_dish->yield/$dish_yield)/100) + $result['f'];
                    $result['i'] = (1*($prod->i * $product->net_weight)*($m_dish->yield/$dish_yield)/100) + $result['i'];
                }
            }
        }
        if($sum == 0){
            $result['yield'] = '-';
            return $result;
        }
        //для продуктов в малой концентрации округление должно быть до 2х знаков.
        if($product_id == 8 || $product_id == 218 || $product_id == 10 || $product_id == 240 || $product_id == 11){
            $result['yield'] = round($sum, 2);
            return  $result;
        }

        $result['yield'] = round($sum, 2);
        return  $result;



        //return 'ok';
    }
	
    public function get_super_total_yield_day($product_id, $menu_id, $brutto_or_netto){
        $menus_dishes = MenusDishes::find()->where(['menu_id' => $menu_id,])->all();
        $sum = 0;
        //$field = 'gross_weight';
        if($brutto_or_netto == 0){
            $field = 'gross_weight';
        }
        elseif($brutto_or_netto == 1){
            $field = 'net_weight';
        }
        else{
            return 'ошибка';
        }

        foreach($menus_dishes as $m_dish){
            $product = DishesProducts::find()->where(['dishes_id' => $m_dish->dishes_id, 'products_id' => $product_id])->one();
            $sum = ($product->$field * ($m_dish->yield/100) ) + $sum;
        }
        if($sum == 0){
            return '-';
        }
        return $sum;

        //return 'ok';
    }
}
