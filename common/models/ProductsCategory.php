<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "products_category".
 *
 * В ТАБЛИЦЕ ХРАНЯТСЯ КАТЕГОРИИ ПРОДУКТОВ()
 *
 * @property int $id
 * @property string $name
 * @property string $created_at
 */
class ProductsCategory extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'products_category';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'sort'], 'required'],
            [['sort', 'sort_techmup'], 'integer'],
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
            'name' => 'Название категории',
            'sort' => 'Приоритет в отчетах',
            'sort_techmup' => 'Приоритет в техкарте',
            'created_at' => 'Дата добавления в базу',
        ];
    }

    public function get_fact_storage_yield($product_category_id, $menu_id, $cycle, $data, $brutto_netto){
        if($brutto_netto == 0){
            $brutto_netto = 'net_weight';
        }
        if($brutto_netto == 1){
            $brutto_netto = 'gross_weight';
        }
        $date_of_week = date("w", $data);
        if($date_of_week == 0){
            $date_of_week = 7;
        }
        $menus_dishes = MenusDishes::find()->where(['date_fact_menu' => $data, 'menu_id' => $menu_id])->all();

        if(!empty($menus_dishes)){
            //return "ok";
            $sum = 0;
            $products_of_category = Products::find()->where(['products_category_id' => $product_category_id])->all();
            //return $products_of_category;
            foreach ($products_of_category as $product_cat){
                foreach ($menus_dishes as $m_dish){
                    $yield = Dishes::findOne($m_dish->dishes_id)->yield;
                    $product = DishesProducts::find()->where(['dishes_id' => $m_dish->dishes_id, 'products_id' => $product_cat->id])->one();
                    if(!empty($product) && $yield > 0){
                        $sum = ($product->$brutto_netto * ($m_dish->yield / $yield)) + $sum;
                    }

                }
            }
            //return $sum.'f';
        }
        else{
            $menus_dishes = MenusDishes::find()->where(['date_fact_menu' => 0, 'menu_id' => $menu_id, 'cycle' => $cycle, 'days_id'=> $date_of_week])->all();
            $sum = 0;
            $products_of_category = Products::find()->where(['products_category_id' => $product_category_id])->all();
            foreach ($products_of_category as $product_cat){
                foreach ($menus_dishes as $m_dish){
                    $yield = Dishes::findOne($m_dish->dishes_id)->yield;
                    $product = DishesProducts::find()->where(['dishes_id' => $m_dish->dishes_id, 'products_id' => $product_cat->id])->one();
                    if($yield > 0){$sum = ($product->$brutto_netto * ($m_dish->yield / $yield)) + $sum;}
                }
            }
        }
        if($sum == 0){
            return '-';
        }
        return round($sum,1);

        //return 'ok';
    }


    public function get_fact_feed_him_sostav_for_category($product_category_id, $menu_id, $data_start, $data_end, $masives)
    {

        $products_of_category = Products::find()->where(['products_category_id' => $product_category_id])->all();
        $mas = [];
        //$mas[] = 1;
        foreach ($masives as $masiv)
        {
            $elements = explode("_", $masiv);
            $data = $elements[0];
            $cycle = $elements[1];
            $day = date("w", $elements[0]);
            if ($day == 0)
            {
                $day = 7;
            }
            foreach ($products_of_category as $product_cat)
            {
                $mas[] =$product_cat->id;
                $menus_dishes = MenusDishes::find()->where(['date_fact_menu' => $data, 'menu_id' => $menu_id, 'cycle' => $cycle, 'days_id' => $day])->all();//fact
                if (empty($menus_dishes))
                {
                    $menus_dishes = MenusDishes::find()->where(['date_fact_menu' => 0, 'menu_id' => $menu_id, 'cycle' => $cycle, 'days_id' => $day])->all();//тогда ищем в циклическом}
                }
                    foreach ($menus_dishes as $m_dish)
                    {
                        //$mas[] = $m_dish->id;
                        //$yield = Dishes::findOne($m_dish->dishes_id)->yield;
                        $product = DishesProducts::find()->where(['dishes_id' => $m_dish->dishes_id, 'products_id' => $product_cat->id])->one();
                        $my_product = Products::findOne($product->products_id);
                        //$mas[] = $product->id;
                        if (!empty($product))
                        {
                            //$mas[] =1;

                            $mas['protein'] = (($m_dish->yield * $my_product->protein) / 100) + $mas['protein'];
                            $mas['fat'] = (($m_dish->yield * $my_product->fat) / 100) + $mas['fat'];
                            $mas['carbohydrates_total'] = (($m_dish->yield * $my_product->carbohydrates_total) / 100) + $mas['carbohydrates_total'];
                            $mas['energy_kkal'] = (($m_dish->yield * $my_product->energy_kkal) / 100) + $mas['energy_kkal'];
                            $mas['carbohydrates_saccharide'] = (($m_dish->yield * $my_product->carbohydrates_saccharide) / 100) + $mas['carbohydrates_saccharide'];
                            $mas['carbohydrates_starch'] = (($m_dish->yield * $my_product->carbohydrates_starch) / 100) + $mas['carbohydrates_starch'];
                            $mas['carbohydrates_lactose'] = (($m_dish->yield * $my_product->carbohydrates_lactose) / 100) + $mas['carbohydrates_lactose'];
                            $mas['carbohydrates_sacchorose'] = (($m_dish->yield * $my_product->carbohydrates_sacchorose) / 100) + $mas['carbohydrates_sacchorose'];
                            $mas['carbohydrates_cellulose'] = (($m_dish->yield * $my_product->carbohydrates_cellulose) / 100) + $mas['carbohydrates_cellulose'];
                            $mas['dust_total'] = (($m_dish->yield * $my_product->dust_total) / 100) + $mas['dust_total'];
                            $mas['dust_nacl'] = (($m_dish->yield * $my_product->dust_nacl) / 100) + $mas['dust_nacl'];

                            $mas['apple_acid'] = (($m_dish->yield * $my_product->apple_acid) / 100) + $mas['apple_acid'];
                            $mas['na'] = (($m_dish->yield * $my_product->na) / 100) + $mas['na'];
                            $mas['k'] = (($m_dish->yield * $my_product->k) / 100) + $mas['k'];

                            $mas['i'] = (($m_dish->yield * $my_product->i) / 100) + $mas['i'];
                            $mas['mg'] = (($m_dish->yield * $my_product->mg) / 100) + $mas['mg'];
                            $mas['p'] = (($m_dish->yield * $my_product->p) / 100) + $mas['p'];

                            $mas['fe'] = (($m_dish->yield * $my_product->fe) / 100) + $mas['fe'];
                            $mas['vitamin_a'] = (($m_dish->yield * $my_product->vitamin_a) / 100) + $mas['vitamin_a'];
                            $mas['vitamin_b_carotene'] = (($m_dish->yield * $my_product->vitamin_b_carotene) / 100) + $mas['vitamin_b_carotene'];

                            $mas['vitamin_b1'] = (($m_dish->yield * $my_product->vitamin_b1) / 100) + $mas['vitamin_b1'];
                            $mas['vitamin_b2'] = (($m_dish->yield * $my_product->vitamin_b2) / 100) + $mas['vitamin_b2'];
                            $mas['vitamin_c'] = (($m_dish->yield * $my_product->vitamin_c) / 100) + $mas['vitamin_c'];
                            $mas['vitamin_d'] = (($m_dish->yield * $my_product->vitamin_d) / 100) + $mas['vitamin_d'];
                            $mas['vitamin_pp'] = (($m_dish->yield * $my_product->vitamin_pp) / 100) + $mas['vitamin_pp'];

                        }
                    }
            }
            return $mas;

        }
    }

    public function get_prognos_storage_feed_him_sostav_for_category($product_category_id, $menu_id){

        $menus_dishes = MenusDishes::find()->where(['date_fact_menu' => 0, 'menu_id' => $menu_id])->all();

        if(!empty($menus_dishes)){
            //return "ok";
            $sum = 0;
            $products_of_category = Products::find()->where(['products_category_id' => $product_category_id])->all();
            //return $products_of_category;
            $mas = [];
            foreach ($products_of_category as $product_cat){
                foreach ($menus_dishes as $m_dish){
                    $dishes = Dishes::findOne($m_dish->dishes_id);
                    $belok = 1;
                    $fat = 1;
                    $ugl = 1;
                    if ($dishes->culinary_processing_id != 3){
                        $belok = 0.94;
                        $fat = 0.88;
                        $ugl = 0.91;
                    }
                    $yield = $dishes->yield;
                    $product = DishesProducts::find()->where(['dishes_id' => $m_dish->dishes_id, 'products_id' => $product_cat->id])->one();
                    $my_product = Products::findOne($product->products_id);
                    if(!empty($product)){
                        $mas['protein'] = (($m_dish->yield/$yield*$product->net_weight * $my_product->protein)/100)*$belok +  $mas['protein'];
                        $mas['fat'] = (($m_dish->yield/$yield*$product->net_weight * $my_product->fat)/100)*$fat +  $mas['fat'];
                        $mas['carbohydrates_total'] = (($m_dish->yield/$yield*$product->net_weight * $my_product->carbohydrates_total)/100)*$ugl +  $mas['carbohydrates_total'];
                        //калории расчитываются во вьюшке
                        //$mas['energy_kkal'] = (($m_dish->yield/$yield*$product->net_weight * $my_product->energy_kkal)/100) +  $mas['energy_kkal'];
                        $mas['carbohydrates_saccharide'] = (($m_dish->yield/$yield*$product->net_weight * $my_product->carbohydrates_saccharide)/100) +  $mas['carbohydrates_saccharide'];
                        $mas['carbohydrates_starch'] = (($m_dish->yield/$yield*$product->net_weight * $my_product->carbohydrates_starch)/100) +  $mas['carbohydrates_starch'];
                        $mas['carbohydrates_lactose'] = (($m_dish->yield/$yield*$product->net_weight * $my_product->carbohydrates_lactose)/100) +  $mas['carbohydrates_lactose'];
                        $mas['carbohydrates_sacchorose'] = (($m_dish->yield/$yield*$product->net_weight * $my_product->carbohydrates_sacchorose)/100) +  $mas['carbohydrates_sacchorose'];
                        $mas['carbohydrates_cellulose'] = (($m_dish->yield/$yield*$product->net_weight * $my_product->carbohydrates_cellulose)/100) +  $mas['carbohydrates_cellulose'];
                        $mas['dust_total'] = (($m_dish->yield/$yield*$product->net_weight * $my_product->dust_total)/100) +  $mas['dust_total'];
                        $mas['dust_nacl'] = (($m_dish->yield/$yield*$product->net_weight * $my_product->dust_nacl)/100) +  $mas['dust_nacl'];

                        $mas['apple_acid'] = (($m_dish->yield/$yield*$product->net_weight * $my_product->apple_acid)/100) +  $mas['apple_acid'];
                        $mas['na'] = (($m_dish->yield/$yield*$product->net_weight * $my_product->na)/100) +  $mas['na'];
                        $mas['k'] = (($m_dish->yield/$yield*$product->net_weight * $my_product->k)/100) +  $mas['k'];

                        $mas['i'] = (($m_dish->yield/$yield*$product->net_weight * $my_product->i)/100) +  $mas['i'];
                        $mas['mg'] = (($m_dish->yield/$yield*$product->net_weight * $my_product->mg)/100) +  $mas['mg'];
                        $mas['p'] = (($m_dish->yield/$yield*$product->net_weight * $my_product->p)/100) +  $mas['p'];

                        $mas['fe'] = (($m_dish->yield/$yield*$product->net_weight * $my_product->fe)/100) +  $mas['fe'];
                        $mas['vitamin_a'] = (($m_dish->yield/$yield*$product->net_weight * $my_product->vitamin_a)/100) +  $mas['vitamin_a'];
                        $mas['vitamin_b_carotene'] = (($m_dish->yield/$yield*$product->net_weight * $my_product->vitamin_b_carotene)/100) +  $mas['vitamin_b_carotene'];

                        $mas['vitamin_b1'] = (($m_dish->yield/$yield*$product->net_weight* $my_product->vitamin_b1)/100) +  $mas['vitamin_b1'];
                        $mas['vitamin_b2'] = (($m_dish->yield/$yield*$product->net_weight * $my_product->vitamin_b2)/100) +  $mas['vitamin_b2'];
                        $mas['vitamin_c'] = (($m_dish->yield/$yield*$product->net_weight * $my_product->vitamin_c)/100) +  $mas['vitamin_c'];
                        $mas['vitamin_d'] = (($m_dish->yield/$yield*$product->net_weight * $my_product->vitamin_d)/100) +  $mas['vitamin_d'];
                        $mas['vitamin_pp'] = (($m_dish->yield/$yield*$product->net_weight * $my_product->vitamin_pp)/100) +  $mas['vitamin_pp'];

                    }
                }
            }
            return $mas;
        }
        return false;
    }

    public function get_fact_storage_yield_for_category($product_category_id, $menu_id, $cycle, $date_start, $date_end){
        //$cycle - это цикл первой входящей нашей даты $date_start
        //return $mas['cycle']['current_date']['days']
        $my_menus = Menus::findOne($menu_id);


        $my_days = MenusDays::find()->where(['menu_id' => $menu_id])->all();
        foreach($my_days as $m_day){
            $ids[] = $m_day->days_id;
        }
        foreach($my_days as $m_day){
            if ($m_day->days_id != 7){
                $ids_for_php[] = $m_day->days_id;//МАССИВ ID ДНЕЙ КЛНКРЕЬНОГО МЕНЮ
            }
            else{
                $ids_for_php[] = 0;
            }
        }
        $max_index_day = min($ids);
        $current_date = $date_start;
        $cycle2 = $cycle;



        while($current_date <= $date_end){
                        if (in_array(date("w", $current_date), $ids_for_php)){
                            if($current_date == $date_start){
//<!--                                <td class="text-center"> $cycle.'цикл '. date("w", $current_date).'д/нед';</td>-->
//<!--                                <td class="text-center">$category->get_fact_storage_yield($category->id, $post['menu_id'], $cycle, $current_date);</td>-->
                            }else{
                                if($max_index_day == date("w", $current_date)){ $cycle2 = $cycle2 + 1;
                                    if($cycle2 <= $my_menus->cycle){
//<!--                                      <td class="text-center"> $cycle2.'цикл '. date("w", $current_date).'д/нед';</td>-->
//<!--                                        <td class="text-center">$category->get_fact_storage_yield($category->id, $post['menu_id'], $cycle2, $current_date);</td>-->
                                    }else{
                                        $cycle2 = 1;
//<!--                                        <td class="text-center"> $cycle2.'цикл '. date("w", $current_date).'д/нед';</td>-->
//<!--                                        <td class="text-center">$category->get_fact_storage_yield($category->id, $post['menu_id'], $cycle2, $current_date);</td>-->
                                    }
                                }else{
                                    $mas = 1;
//<!--                                     <td class="text-center">= $cycle2.'цикл '. date("w", $current_date).'д/нед';</td>-->
//<!--                                    <td class="text-center">$category->get_fact_storage_yield($category->id, $post['menu_id'], $cycle2, $current_date);</td>-->
                                }
                            }
                             //$current_date = $current_date + 86400;
                         } else{$current_date = $current_date + 86400;}
                     }



    }

    /*Получает массив для прогнозной ведомости по циклам и дням*/
    public function get_total_yield_category($menu_id){

        $informations = DishesProducts::find()->
        select(['menus_dishes.id as unique','dishes_products.dishes_id','dishes_products.products_id', 'dishes_products.net_weight', 'dishes_products.gross_weight', 'dishes.yield as dishes_yield', 'menus_dishes.cycle as cycle', 'menus_dishes.days_id as days_id', 'menus_dishes.yield as menus_yield', 'products.products_category_id as products_category_id'])->
        leftJoin('products', 'dishes_products.products_id = products.id')->
        leftJoin('menus_dishes', 'dishes_products.dishes_id = menus_dishes.dishes_id')->
        leftJoin('dishes', 'dishes_products.dishes_id = dishes.id')->
        where(['menus_dishes.date_fact_menu' => '0','menus_dishes.menu_id' => $menu_id/*, 'cycle' => [2]*/])->
        asArray()->
        all();

        return $informations;

    }

    /*Получает массив для прогнозной ведомости по приемам пищи*/
    public function get_total_yield_nutrition_category($menu_id){

        $informations = DishesProducts::find()->
        select(['menus_dishes.id as unique','dishes_products.dishes_id','dishes_products.products_id', 'dishes_products.net_weight', 'dishes_products.gross_weight', 'dishes.yield as dishes_yield', 'menus_dishes.nutrition_id as nutrition_id', 'menus_dishes.cycle as cycle', 'menus_dishes.days_id as days_id', 'menus_dishes.yield as menus_yield', 'products.products_category_id as products_category_id'])->
        leftJoin('products', 'dishes_products.products_id = products.id')->
        leftJoin('menus_dishes', 'dishes_products.dishes_id = menus_dishes.dishes_id')->
        leftJoin('dishes', 'dishes_products.dishes_id = dishes.id')->
        where(['menus_dishes.date_fact_menu' => '0','menus_dishes.menu_id' => $menu_id/*, 'cycle' => [2]*/])->
        asArray()->
        all();

        return $informations;

    }

    public function get_total_yield_all_category($menu_id, $cycle, $days_id, $brutto_or_netto){
        $menus_dishes = MenusDishes::find()->where(['date_fact_menu' => 0, 'menu_id' => $menu_id, 'cycle' => $cycle, 'days_id'=> $days_id])->all();
        $sum = 0;
        $products_of_category = Products::find()->all();
        foreach ($products_of_category as $product_cat){
            foreach ($menus_dishes as $m_dish){
                $yield = Dishes::findOne($m_dish->dishes_id)->yield;
                $product = DishesProducts::find()->where(['dishes_id' => $m_dish->dishes_id, 'products_id' => $product_cat->id])->one();
                $sum = ($product->net_weight * ($m_dish->yield / $yield)) + $sum;
            }
        }
        if($sum == 0){
            return '-';
        }
        return $sum;
    }

    public function get_total_field_category($product_category_id, $menu_id, $field){
        $menus_dishes = MenusDishes::find()->where(['menu_id' => $menu_id])->all();
        $sum = 0;
        $products_of_category = Products::find()->where(['products_category_id' => $product_category_id])->all();
        foreach ($products_of_category as $product_cat){
            foreach ($menus_dishes as $m_dish){
                $product = DishesProducts::find()->where(['dishes_id' => $m_dish->dishes_id, 'products_id' => $product_cat->id])->one();
                $product_data = Products::find()->where(['id' => $product->products_id])->one();
                $sum = ($product_data->$field * ($m_dish->yield / 100)) + $sum;
            }
        }
        if($sum == 0){
            return '-';
        }
        return $sum;
    }

    public function get_total_field_category_itog($menu_id, $field){
        $menus_dishes = MenusDishes::find()->where(['menu_id' => $menu_id])->all();
        $sum = 0;
        $products_of_category = Products::find()->all();
        foreach ($products_of_category as $product_cat){
            foreach ($menus_dishes as $m_dish){
                $product = DishesProducts::find()->where(['dishes_id' => $m_dish->dishes_id, 'products_id' => $product_cat->id])->one();
                $product_data = Products::find()->where(['id' => $product->products_id])->one();
                $sum = ($product_data->$field * ($m_dish->yield / 100)) + $sum;
            }
        }
        if($sum == 0){
            return '-';
        }
        return $sum;
    }
	
	public function get_prognos_storage_normativ($category, $menu_id){
        $menu = Menus::findOne($menu_id);
        $organization = Organization::findOne($menu->organization_id);
        $normativ = NormativPrognosStorage::find()->where(['type_organization_id' => $organization->type_org, 'age_info_id' => $menu->age_info_id, 'products_category_id' => $category])->one()->value;
        if(empty($normativ)){
            $normativ = '-';
        }
        return $normativ;
    }
}

