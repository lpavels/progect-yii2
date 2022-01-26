<?php

namespace common\models;

use Yii;
use common\models\Dishes;
use common\models\DishesProducts;
use common\models\MenusNutrition;
use common\models\RecipesCollection;
use common\models\Products;

/**
 * This is the model class for table "menus_dishes".
 *
 * @property int $id
 * @property int $menu_id
 * @property int $days_id
 * @property int $nutrition_id
 * @property int $dishes_id
 * @property float $yield
 * @property string $created_at
 */
class MenusDishes extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'menus_dishes';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['menu_id', 'days_id', 'nutrition_id', 'dishes_id', 'yield', 'cycle', 'date_fact_menu'], 'required'],
            [['menu_id', 'days_id', 'nutrition_id', 'dishes_id', 'date_fact_menu'], 'integer'],
            [['yield'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            //'id' => 'ID',
            'menu_id' => 'Меню',
            'days_id' => 'День',
            'nutrition_id' => 'Прием пищи',
            'dishes_id' => 'Блюдо',
            'yield' => 'Выход (грамм)',
            'cycle' => '№ недели(цикл)',
            'created_at' => 'Дата добавления',
        ];
    }

    public function get_cycle_from_date($menu_id, $date){
        $my_menus = Menus::findOne($menu_id);

        $start_date = date('d.m.Y', $my_menus->date_start);//Дата старта меню
        $day_of_week = date("w", strtotime($date));//День недели выбранной даты
        $day_of_week_start_date = date("w", strtotime($start_date));//День недели даты старта меню
        /*ПРОБЛЕМА В ТОМ ЧТО ДНИ НЕДЕЛИ В БАЗЕ ХРАНЯТСЯ С ID ОТ 1-7(ВОСКРЕСЕНЬЕ - 7), А PHP ВОСКРЕСЕНЬЕ - 0 ДЕНЬ, ПОЭТОМУ НУЖЕН ELSE ЧТОБЫ 7 ЗАМЕНИТЬ НА 0*/
        /*ПЕРЕОПРЕДЕЛЯЕМ ОБРАТНО ДЕЛАЕМ ВОСКРЕСЕНЬЕ 7М ДНЕМ*/
        if ($day_of_week_start_date == 0)
        {
            $day_of_week_start_date = 7;
        }
        if ($day_of_week == 0)
        {
            $day_of_week = 7;
        }
        /*КОНЕЦ ПЕРЕОПРЕДЕЛЕНИЯ*/
        $day_offset = $day_of_week_start_date - 1;//СКОЛЬКО ДНЕЙ НУЖНО ОТНИМАТЬ ДЛЯ ТОГО ЧТОБЫ ПЕРЕЙТИ К ПОНЕДЕЛЬНИКУ

        $date_monday = date('d.m.Y', strtotime(($start_date) . ' - ' . $day_offset . ' day'));//ДАТА ПОНЕДЕЛЬНИКА САМОГО ПЕРВОГО
        $dif_monday_and_start = ceil(((strtotime($start_date)) - (strtotime($date_monday))) / 86400);//РАЗНИЦА МЕЖДУ ПОНЕДЕЛЬНИКОМ И СТАРТОВОЙ ДАТЫ В ДНЯХ
        $count_week = ceil((((strtotime($date) - $my_menus->date_start) / 86400) + $dif_monday_and_start) / 7);//РАСЧЕТ КОЛИЧЕСТВА НЕДЕЛЬ МЕЖДУ ВЫБРАННОЙ ДАТОЙ И ПОНЕДЕЛЬНИКОМ КОТОРЫЙ САМЫЙ ПЕРВЫЙ

        $cycle = $count_week;//ПРИРАВНИВАЕМ ЦИКЛ КОЛИЧЕСТВУ НЕДЕЛЬ ДО НАШЕЙ ДАТЫ
        /*ЕСЛИ ВЫБРАННЫЙ ДЕНЬ ЯВЛЯЕТСЯ ПОНЕДЕЛЬНИКОМ, ТО ПРОГРАММА СЧИТАЕТ РАЗНИЦУ МЕЖДУ ДВУМЯ ПОНЕДЕЛЬНИКАМИ, СООТВЕТСТВЕННО ОШИБОЧНО ПРИБАВЛЯЕТСЯ ЛИШНЯЯ НЕДЕЛЯ, ПОЭТОМУ ЕЕ СЛЕДУЮТ УБИРАТЬ. ТАК КАК МЫ ИЩЕМ ПОНЕДЕЛЬНИК( И ОН МОЖЕТ И НЕ ВХОДИТ В ДИАПОЗОН СТАРТА И ОКОНЧАНИЯ, ВОЗНИКАЕТ ОШИБКА ОПРЕДЕЛЕНИЯ ЦИКЛА. СЛЕДУЮЩЕЕ УСЛОВИЕЕ ЕЕ ИСПРАВЛЯЕТ)*/
        if ($day_of_week == 1)
        {
            $cycle = $count_week - 1;
        }
        /*$date_monday дата понедельника с которого идет отсчет. ПРОБЛЕМА В ТОМ ЧТО ЭТОТ ПОНЕДЕЛЬНИК МОЖЕТ ЯВЛЯТЬСЯ ПЕРВЫМ ДНЕМ НАШЕГО МЕНЮ И СООТВЕТСТВЕННО РАЗНИЦА МЕЖДУ ЭТИМИ ДНЯМИ БУДЕТ 0 И ЦИКЛ СООТВЕТСТВЕННО -1. ПОЭТОМУ В ЭТОМ СЛУЧАЕ МЫ НАЗНАЧАЕМ ТАКОЙ ПОНЕДЕЛЬНИК ПЕРВОЙ НЕДЕЛЕЙ*/
        if ($count_week == 0)
        {
            $cycle = 1;
        }

        /*ПРОЦЕСС ИЗМЕНЕНИЯ ЦИКЛА ВЗАВИСИМОСТИ ОТ КОЛИЧЕСТВО НЕДЕЛЬ*/
        while ($cycle > $my_menus->cycle)
        {
            $cycle = $cycle - $my_menus->cycle;
        }
        if ($cycle == 0)
        {
            $cycle = $my_menus->cycle;
        }
        if($cycle < 0){
            return 'errror';
        }
        /*КОНЕЦ ПРОЦЕССА ИЗМЕНЕИЯ ЦИКЛАБ ДАЛЕЕ ЦИКЛ ОТПРАВЛЯЕМ ВО VIEW*/

        return $cycle;
    }

    public function get_dishes($id){
        $category = Dishes::findOne($id);
        return $category->name;
    }



    public function get_menus($id){
        $d = Menus::findOne($id);
        return $d->name;
    }
    public function get_nutrition($id){
        $category = NutritionInfo::findOne($id);
        return $category->name;
    }

    public function get_days($id){
        if($id == 1){
            return 'Понедельник';
        }
        if($id == 2){
            return 'Вторник';
        }
        if($id == 3){
            return 'Среда';
        }
        if($id == 4){
            return 'Четверг';
        }
        if($id == 5){
            return 'Пятница';
        }
        if($id == 6){
            return 'Суббота';
        }
        /*0 потому что в встроенных функциях php  воскресенье это нулевой день, а в базе он у нас как 7й день*/
        if($id == 7){
            return 'Воскресенье';
        }
        if($id == 0){
            return 'Воскресенье';
        }
        return 'Не определено';
        //return $id;
    }



    //функции для menu-days
    public function get_yield($dishes_id){

        $dishes = Dishes::findOne($dishes_id);
        return $dishes->yield;
    }


    public function get_field($dishes_id, $yield, $field){
        $dishes_yield = $this->get_yield($dishes_id);
        $dishes_products = DishesProducts::find()->where(['dishes_id' => $dishes_id])->all();
        $sum = 0;
        foreach($dishes_products as $d_product){
            $product = Products::findOne($d_product->products_id);
            $protein = ($product->$field * $d_product->net_weight)/100;
            $sum = $sum + $protein;
        }
        $sum = $sum*($yield/$dishes_yield);
        return $sum;
    }
    /*получение калорий продукта в блюде с учетом уварки белков жиров и углеводов*/
    public function get_kkal($id, $dishes_id){
        /*ПОИСК ЗАМЕНЫ. зАМЕНЯЛ ЛИ ПОЛЬЗОВАТЕЛЬ ПРОДУКТ НА ИНОЙ.*/
        $products_change = ProductsChangeOrganization::find()->where(['organization_id' => Yii::$app->user->identity->organization_id, 'products_id' => $id])->one();
        if(!empty($products_change)){
            $id = $products_change->change_products_id;
        }
        /*КОНЕЦ ЗАМЕНЫ*/

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


    public function get_vitamin($dishes_id, $yield, $field){
        $dishes_yield = $this->get_yield($dishes_id);
        $dish = Dishes::findOne($dishes_id);

        $dishes_products = DishesProducts::find()->where(['dishes_id' => $dishes_id])->all();
        $sum = 0;
        $uvarka = 1;
        if ($dish->culinary_processing_id != 3){
            if($field == 'vitamin_a'){
                $uvarka = 0.6;
            }
            elseif($field == 'vitamin_b1'){
                $uvarka = 0.72;
            }
            elseif($field == 'vitamin_c'){
                $uvarka = 0.40;
            }
            elseif($field == 'vitamin_pp' || $field == 'vitamin_b2' || $field == 'vitamin_b_carotene'){
                $uvarka = 0.8;
            }
            elseif($field == 'mg' || $field == 'p' || $field == 'fe'){
                $uvarka = 0.87;
            }
            elseif($field == 'ca' || $field == 'se'){
                $uvarka = 0.88;
            }
            elseif($field == 'na'){
                $uvarka = 0.76;
            }
            elseif($field == 'k'){
                $uvarka = 0.83;
            }
            else{
                $uvarka = 1;
            }
        }

        foreach($dishes_products as $d_product){
            /*ПОИСК ЗАМЕНЫ. зАМЕНЯЛ ЛИ ПОЛЬЗОВАТЕЛЬ ПРОДУКТ НА ИНОЙ.*/
            $products_change = ProductsChangeOrganization::find()->where(['organization_id' => Yii::$app->user->identity->organization_id, 'products_id' => $d_product->products_id])->one();
            if(!empty($products_change)){
                $d_product->products_id = $products_change->change_products_id;
            }
            /*КОНЕЦ ЗАМЕНЫ*/

            $product = Products::findOne($d_product->products_id);
            $protein = ($product->$field * $d_product->net_weight)/100;
            $sum = $sum + $protein;
        }
        if($dishes_yield>0){
            $sum = $sum*($yield/$dishes_yield) * $uvarka;
        }else{
            $sum = 0;
        }

        return $sum;
    }


    /*РАСЧЕТ белков/жиров/углеводов для отдельного продукта С КОЕФИЦИЕНТАМИ уварки ИСПОЛЬЗУЕТСЯ ВО ВСЕМ КОНТРОЛЛЕРЕ МЕНЮСДИШЕС*/
    public function get_products_bju($id, $dishes_id, $field){
        /*ПОИСК ЗАМЕНЫ. зАМЕНЯЛ ЛИ ПОЛЬЗОВАТЕЛЬ ПРОДУКТ НА ИНОЙ.*/
        $products_change = ProductsChangeOrganization::find()->where(['organization_id' => Yii::$app->user->identity->organization_id, 'products_id' => $id])->one();
        if(!empty($products_change)){
            $id = $products_change->change_products_id;
        }
        /*КОНЕЦ ЗАМЕНЫ*/
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


    /*функция для расчета белков/жиров/углеводов для всего блюда с учетом уварки его продуктов*/
    public function get_bju_dish($id, $field){
        $m_dishes = MenusDishes::findOne($id);
        $dishes = Dishes::findOne($m_dishes->dishes_id);
        $total = 0;
        $dishes_products = DishesProducts::find()->where(['dishes_id' => $m_dishes['dishes_id']])->all();
        foreach($dishes_products as $d_product){
            $kkal = $this->get_products_bju($d_product->products_id, $m_dishes->dishes_id, $field) * ($d_product->net_weight/100) *($m_dishes->yield / $dishes->yield);/**/
            $total = $total + $kkal;
        }
        return $total;
    }

    /*функция для расчета белков/жиров/углеводов для всего блюда с учетом уварки его продуктов С УКАЗАННЫМ ВЫХОДОМ*/
    public function get_bju_dish_with_your_yield($dishes_id, $field, $yield){
        $dishes = Dishes::findOne($dishes_id);
        $total = 0;
        $dishes_products = DishesProducts::find()->where(['dishes_id' => $dishes_id])->all();
        foreach($dishes_products as $d_product){
            $kkal = $this->get_products_bju($d_product->products_id, $dishes_id, $field) * ($d_product->net_weight/100) *($yield / $dishes->yield);/**/
            $total = $total + $kkal;
        }
        return $total;
    }


    //Итого выход за определенный прием пищи
    public function get_total_yield($menu_id, $cycle, $days_id, $nutrition_id){
        $menus_dishes = MenusDishes::find()->where(['date_fact_menu' => 0, 'menu_id' => $menu_id, 'cycle' => $cycle, 'days_id'=> $days_id, 'nutrition_id' => $nutrition_id])->all();
        $total = 0;
        foreach($menus_dishes as $m_dishes){
            $total = $total + $m_dishes->yield;
        }
        return $total;
    }


    //Итого по по полям за прием пищи
    public function get_total_field($menu_id, $cycle, $days_id, $nutrition_id, $field){
        $menus_dishes = MenusDishes::find()->where(['date_fact_menu' => 0, 'menu_id' => $menu_id, 'cycle' => $cycle, 'days_id'=> $days_id, 'nutrition_id' => $nutrition_id])->all();
        $total = 0;
        foreach($menus_dishes as $m_dishes){
            $total = $total + $this->get_field($m_dishes->dishes_id, $m_dishes->yield, $field);
        }
        return $total;
    }
    /*сумма белков жиров и углеводов за прием пищи*/
    public function get_bju_nutrition($menu_id, $cycle, $days_id, $nutrition_id, $field){
        $menus_dishes = MenusDishes::find()->where(['date_fact_menu' => 0, 'menu_id' => $menu_id, 'cycle' => $cycle, 'days_id'=> $days_id, 'nutrition_id' => $nutrition_id])->all();
        $total = 0;
        foreach($menus_dishes as $m_dishes){
            $total = $total + $this->get_bju_dish($m_dishes->id, $field);
        }
        return $total;
    }

    //калории за прием пищи
    public function get_total_kkal($menu_id, $cycle, $days_id, $nutrition_id){
        $menus_dishes = MenusDishes::find()->where(['date_fact_menu' => 0, 'menu_id' => $menu_id, 'cycle' => $cycle, 'days_id'=> $days_id, 'nutrition_id' => $nutrition_id])->all();
        $total = 0;
        foreach($menus_dishes as $m_dishes){

            $total = $total + $this->get_kkal($m_dishes->dishes_id, $m_dishes->yield);
        }
        return $total;
    }

    //калории за прием пищи
    public function get_kkal_nutrition($menu_id, $cycle, $days_id, $nutrition_id){
        $menus_dishes = MenusDishes::find()->where(['date_fact_menu' => 0, 'menu_id' => $menu_id, 'cycle' => $cycle, 'days_id'=> $days_id, 'nutrition_id' => $nutrition_id])->all();
        $total = 0;
        foreach($menus_dishes as $m_dishes){

            $total = $total + $this->get_kkal_dish($m_dishes->id);
        }
        return $total;
    }

    public function get_kkal_dish($id){//за блюдо
        $m_dishes = MenusDishes::findOne($id);
        $dishes = Dishes::findOne($m_dishes->dishes_id);
        $total = 0;
        $dishes_products = DishesProducts::find()->where(['dishes_id' => $m_dishes['dishes_id']])->all();
        foreach($dishes_products as $d_product){
            $kkal = $this->get_kkal($d_product->products_id, $m_dishes->dishes_id) * ($d_product->net_weight/100) *($m_dishes->yield / $dishes->yield);/**/
            $total = $total + $kkal;
        }
        return $total;
    }


    //Итого по витаминам
    public function get_total_vitamin($menu_id, $cycle, $days_id, $nutrition_id, $field){
        $menus_dishes = MenusDishes::find()->where(['date_fact_menu' => 0, 'menu_id' => $menu_id, 'cycle' => $cycle, 'days_id'=> $days_id, 'nutrition_id' => $nutrition_id])->all();
        $total = 0;
        foreach($menus_dishes as $m_dishes){
            $total = $total + $this->get_vitamin($m_dishes->dishes_id, $m_dishes->yield, $field);
        }
        return $total;
    }


    //Процентное соотношение БЖУ
    public function get_bju($menu_id, $cycle, $days_id, $nutrition_id, $field){
        $my_field = $this->get_bju_nutrition($menu_id, $cycle, $days_id, $nutrition_id, $field);
        $protein = $this->get_bju_nutrition($menu_id, $cycle, $days_id, $nutrition_id, 'protein');
        if($protein != 0){
            $total = ($my_field/$protein);
        }
        return round($total, 2);
    }


    //Процент от общей массы пищевых веществ
    public function get_procent($menu_id, $cycle, $days_id, $nutrition_id, $field){
        $total = 0;
        if($field == 'protein'){
            $protein = $this->get_bju_nutrition($menu_id, $cycle, $days_id, $nutrition_id, $field);
            $indicator = $protein;
        }
        else{
            $protein = $this->get_bju_nutrition($menu_id, $cycle, $days_id, $nutrition_id, 'protein');
        }
        if($field == 'fat'){
            $fat = $this->get_bju_nutrition($menu_id, $cycle, $days_id, $nutrition_id, $field);
            $indicator = $fat;
        }
        else{
            $fat = $this->get_bju_nutrition($menu_id, $cycle, $days_id, $nutrition_id, 'fat');
        }
        if($field == 'carbohydrates_total'){
            $carbohydrates_total = $this->get_bju_nutrition($menu_id, $cycle, $days_id, $nutrition_id, $field);
            $indicator = $carbohydrates_total;
        }
        else{
            $carbohydrates_total = $this->get_bju_nutrition($menu_id, $cycle, $days_id, $nutrition_id, 'carbohydrates_total');
        }

        $total = $protein + $fat + $carbohydrates_total;
        if($total != 0){
            $total = ($indicator / $total) * 100;
        }
        return round($total, 1);
    }


    public function get_super_total_yield($menu_id, $cycle, $days_id, $nutrition_id){
        $menus_dishes = MenusDishes::find()->where(['date_fact_menu' => 0, 'menu_id' => $menu_id, 'cycle' => $cycle, 'days_id'=> $days_id])->all();
        $total = 0;
        foreach($menus_dishes as $m_dishes){
            $total = $total + $m_dishes->yield;
        }
        if($nutrition_id == 'super_total'){
            return $total;
        }
        if($total == 0){
            return 0;
        }
        $procent = ($this->get_total_yield($menu_id, $cycle, $days_id, $nutrition_id)/$total)*100;

        return round($procent, 1);
    }


    /*процент от суток с учетом уварок*/
    public function get_super_total_field($menu_id, $cycle, $days_id, $nutrition_id, $field){
        $menus_dishes = MenusDishes::find()->where(['date_fact_menu' => 0, 'menu_id' => $menu_id, 'cycle' => $cycle, 'days_id'=> $days_id])->all();
        $menus_nutritions = MenusNutrition::find()->where(['menu_id'=>$menu_id])->all();
        $total = 0;
        foreach($menus_nutritions as $m_nutrition){
            $total = $total + $this->get_bju_nutrition($menu_id, $cycle, $days_id, $m_nutrition->nutrition_id, $field);
        }
        if($nutrition_id == 'super_total'){
            return $total;
        }
        $procent = ($this->get_bju_nutrition($menu_id, $cycle, $days_id, $nutrition_id, $field)/$total)*100;
        return round($procent, 1);
    }


    public function get_super_total_kkal($menu_id, $cycle, $days_id, $nutrition_id){
        if (!$menu_id|| !$cycle|| !$days_id|| !$nutrition_id)
        {
            return 0;
        }
        //$menus_dishes = MenusDishes::find()->where(['date_fact_menu' => 0, 'menu_id' => $menu_id, 'cycle' => $cycle, 'days_id'=> $days_id])->all();
        $menus_nutritions = MenusNutrition::find()->where(['menu_id'=>$menu_id])->all();
        $total = 0;
        if (!empty($menus_nutritions))
        {
            foreach($menus_nutritions as $m_nutrition){
                $total = $total + $this->get_kkal_nutrition($menu_id, $cycle, $days_id, $m_nutrition->nutrition_id);
            }
            if($nutrition_id == 'super_total'){
                return $total;
            }
            $procent = ($this->get_kkal_nutrition($menu_id, $cycle, $days_id, $nutrition_id)/$total)*100;
            return round($procent, 1);
        }
        else
        {
            return 0;
        }
    }

    public function get_super_total_kkal_report($menu_id, $cycle, $days_id, $nutrition_id){
        if (!$menu_id|| !$cycle|| !$days_id|| !$nutrition_id)
        {
            return 0;
        }

        //$menus_dishes = MenusDishes::find()->where(['date_fact_menu' => 0, 'menu_id' => $menu_id, 'cycle' => $cycle, 'days_id'=> $days_id])->all();
        $menus_nutritions = MenusNutrition::find()->where(['menu_id'=>$menu_id])->all();
        $total = 0;
        if (!empty($menus_nutritions))
        {
            if($nutrition_id == 'super_total'){

            }
            elseif ($nutrition_id == 'super_total'){
                foreach($menus_nutritions as $m_nutrition){
                    $total = $total + $this->get_kkal_nutrition($menu_id, $cycle, $days_id, $m_nutrition->nutrition_id);
                }
                return $total;
            }
            //$procent = ($this->get_kkal_nutrition($menu_id, $cycle, $days_id, $nutrition_id)/$total)*100;
            return round($total, 1);
        }
        else
        {
            return 0;
        }
    }


    public function get_super_total_vitamin($menu_id, $cycle, $days_id, $field){
        //$menus_dishes = MenusDishes::find()->where(['date_fact_menu' => 0, 'menu_id' => $menu_id, 'cycle' => $cycle, 'days_id'=> $days_id])->all();
        $menus_nutritions = MenusNutrition::find()->where(['menu_id'=>$menu_id])->all();
        $total = 0;
        foreach($menus_nutritions as $m_nutrition){
            $total = $total + $this->get_total_vitamin($menu_id, $cycle, $days_id, $m_nutrition->nutrition_id, $field);
        }
        return $total;

    }
    /*соотношение бжу за 1 день*/
    public function get_super_total_bju($menu_id, $cycle, $days_id, $nutrition_id, $field){
        $my_field = $this->get_super_total_field($menu_id, $cycle, $days_id, $nutrition_id, $field);
        $protein = $this->get_super_total_field($menu_id, $cycle, $days_id, $nutrition_id, 'protein');
        if($protein != 0){
            $total = ($my_field/$protein)*100;
        }
        return round($total, 1);
    }


    public function get_recommended_normativ($menu_id, $nutrition_id, $field){
        $my_menus = Menus::findOne($menu_id);
        $age_id = $my_menus->age_info_id;
        $normativ = NormativInfo::find()->where(['age_info_id' => $age_id, 'nutrition_info_id' => $nutrition_id])->one();
        return $normativ->$field;
        //return 1;
    }

    public function get_recommended_normativ_of_day($menu_id, $field){
        $my_menus = Menus::findOne($menu_id);
        $age_id = $my_menus->age_info_id;
        $normativ = NormativInfo::find()->where(['age_info_id' => $age_id, 'nutrition_info_id' => 0])->one();
        return $normativ->$field;
        //return 1;
    }

    public function get_techmup($menu_id){
        $my_menus = Dishes::findOne($menu_id);
        return $my_menus->techmup_number;
        //return 1;
    }
    public function get_recipes($menu_id){
        $my_menus = RecipesCollection::findOne($menu_id);
        return $my_menus->name;
        //return 1;
    }

    public function insert_info($menu_id, $field){

        if($field == 'feeders_characters'){
            $menus = Menus::findOne($menu_id);
            $feeders_characters = FeedersCharacters::findOne($menus->feeders_characters_id);
            return $feeders_characters->name;
        }

        if($field == 'age_info'){
            $menus = Menus::findOne($menu_id);
            $age = AgeInfo::findOne($menus->age_info_id);

            return $age->name;
        }

        if($field == 'sroki'){
            $menus_days = Menus::findOne($menu_id);
            return date("d.m.Y", $menus_days->date_start).' - '.date("d.m.Y", $menus_days->date_end);
        }

        if($field == 'days'){
            $menus_days = MenusDays::find()->where(['menu_id' => $menu_id])->all();
            $days = '';
            foreach($menus_days as $m_day){
                $days.= Days::findOne($m_day->days_id)->name. ' ';
            }

            return $days;
        }

        //return 'not ok';
    }

    public function get_total_raskladka_yield($post_menus, $menu_dishes, $data){
        //$menu_dishes = 411;
        $total_informations = [];
        foreach($post_menus as $p_menu)
        {
            $my_menus_dishes = MenusDishes::find()->where(['menu_id' => $p_menu->id])->andWhere(['id' => $menu_dishes]);
            $md = MenusDishes::find()->where(['menu_id' => $p_menu->id, 'date_fact_menu' => $data])->count();
            if ($md > 0)
            {
                $informations = DishesProducts::find()->
                select(['menus_dishes.id as unique', 'dishes_products.dishes_id', 'dishes_products.products_id', 'dishes_products.net_weight', 'dishes_products.gross_weight', 'dishes.yield as dishes_yield', 'menus_dishes.cycle as cycle', 'menus_dishes.nutrition_id', 'menus_dishes.days_id as days_id', 'menus_dishes.yield as menus_yield', 'menus_dishes.menu_id as menu_id'])->
                leftJoin('menus_dishes', 'dishes_products.dishes_id = menus_dishes.dishes_id')->
                leftJoin('dishes', 'dishes_products.dishes_id = dishes.id')->
                where(['menus_dishes.date_fact_menu' => $data, 'menus_dishes.menu_id' => $p_menu->id, 'menus_dishes.id' => $menu_dishes])->
                asArray()->
                all(); //print_r($informations);exit;
            }
            else
            {
                $informations = DishesProducts::find()->
                select(['menus_dishes.id as unique', 'dishes_products.dishes_id', 'dishes_products.products_id', 'dishes_products.net_weight', 'dishes_products.gross_weight', 'dishes.yield as dishes_yield', 'menus_dishes.cycle as cycle', 'menus_dishes.nutrition_id', 'menus_dishes.days_id as days_id', 'menus_dishes.yield as menus_yield', 'menus_dishes.menu_id as menu_id'])->
                leftJoin('menus_dishes', 'dishes_products.dishes_id = menus_dishes.dishes_id')->
                leftJoin('dishes', 'dishes_products.dishes_id = dishes.id')->
                where(['menus_dishes.date_fact_menu' => '0', 'menus_dishes.menu_id' => $p_menu->id, 'menus_dishes.id' => $menu_dishes])->
                asArray()->
                all();
            }
            $total_informations = array_merge($total_informations, $informations);
            //print_r($menu_dishes);exit;
        }

        return $total_informations;

    }

    public function get_allergen_dish($dishes_id, $mass){
        $mass_check = [];
        $mass_prod = [];
        $result = '';
        $dishes_products = DishesProducts::find()->where(['dishes_id' => $dishes_id])->all();
        foreach ($dishes_products as $d_prod){
            $mas_prod[] = $d_prod->products_id;
        }
        $products_allergen = ProductsAllergen::find()->where(['products_id' => $mas_prod])->all();
        foreach($products_allergen as $p_al){
            foreach($mass as $mas){

                if($p_al->allergen_id == $mas && !array_key_exists($mas, $mass_check)){
                    $mass_check[$mas] = Allergen::findOne($mas)->name;
                }
            }
        }
        foreach ($mass_check as $m){
            if($result != ''){
                $result .= ',<br>'.$m;
            }
            else{
                $result .= $m;
            }

        }
        if($result == ''){
            $result = '-';
        }
        return $result;
    }


    public function get_max_normativ_he($menu_id, $nutrition_id)
    {
        $age_info_id = Menus::findOne($menu_id)->age_info_id;
        $max = NormativHe::find()->where(['nutrition_id' => $nutrition_id, 'age_info_id' =>$age_info_id])->max('max_value');

        return $max;
    }


    public function get_normativ_he_for_itog_nutrition($menu_id, $nutrition_id)
    {
        $age_info_id = Menus::findOne($menu_id)->age_info_id;
        $normatives = NormativHe::find()->where(['nutrition_id' => $nutrition_id, 'age_info_id' =>$age_info_id])->all();
        $result = '';
        if(!empty($normatives)){
            foreach($normatives as $norma){
                if($norma->sex == 3){
                    if($norma->min_value == $norma->max_value){$result = $norma->max_value;}
                    else{$result = $norma->min_value.' - '.$norma->max_value;}
                }
                if($norma->sex == 1){
                    if($norma->min_value == $norma->max_value){$result .= 'Мальчики: '.$norma->max_value. '<br>';}
                    else{$result .= 'Мальчики: '. $norma->min_value.' - '.$norma->max_value. '<br>';}
                }
                if($norma->sex == 2){
                    if($norma->min_value == $norma->max_value){$result .= 'Девочки: '.$norma->max_value. '<br>';}
                    else{$result .= 'Девочки: '. $norma->min_value.' - '.$norma->max_value. '<br>';}
                }
            }
        }
        if($result == ''){
            $result = 'Нет данных';
        }
        return $result;
    }

    public function get_normativ_he_for_itog_day($menu_id)
    {
        $age_info_id = Menus::findOne($menu_id)->age_info_id;
        $normatives = NormativHe::find()->where(['nutrition_id' => 0, 'age_info_id' =>$age_info_id])->all();
        $result = '';
        if(!empty($normatives)){
            foreach($normatives as $norma){
                if($norma->sex == 3){
                    if($norma->min_value == $norma->max_value){$result = $norma->max_value;}
                    else{$result = $norma->min_value.' - '.$norma->max_value;}
                }
                if($norma->sex == 1){
                    if($norma->min_value == $norma->max_value){$result .= 'Мальчики: '.$norma->max_value. '<br>';}
                    else{$result .= 'Мальчики: '. $norma->min_value.' - '.$norma->max_value. '<br>';}
                }
                if($norma->sex == 2){
                    if($norma->min_value == $norma->max_value){$result .= 'Девочки: '.$norma->max_value. '<br>';}
                    else{$result .= 'Девочки: '. $norma->min_value.' - '.$norma->max_value. '<br>';}
                }
            }
        }
        if($result == ''){
            $result = 'Нет данных';
        }
        return $result;
    }






	public function get_menu_information($organization_id, $nutrition){
        $total_informations = [];


        $menu_dishes_model = New \common\models\MenusDishes();
        $menu_ids = [];
        $menus = \common\models\Menus::find()->where(['organization_id' => $organization_id, 'feeders_characters_id' => 3, 'age_info_id' => [6, 9], 'cycle' => [2,3,4], 'status_archive' => 0])->all();
        foreach($menus as $menu){
            if(MenusNutrition::find()->where(['menu_id' => $menu->id, 'nutrition_id' =>$nutrition])->count() > 0){
                $menu_ids[] = $menu->id;
            }

        }
        if(count($menu_ids) > 0){
            $menus = \common\models\Menus::find()->where(['id' => $menu_ids])->all();

            $min_massa_dishes = 1000;
            $max_massa_dishes = 0;
            $min_kkal = 10000;
            $max_kkal = 0;
            
            $count_nutrition = 0;
            foreach($menus as $menu){
                $cycles = $menu->cycle;
                $menus_days = MenusDays::find()->where(['menu_id' => $menu->id])->orderby(['days_id' => SORT_ASC])->all();
                for($i_cycle=1;$i_cycle<=$cycles;$i_cycle++)
                {
                    foreach ($menus_days as $day)
                    {
                        $yield = $menu_dishes_model->get_total_yield($menu->id, $i_cycle, $day->days_id, $nutrition);
                        if($yield > 0){
                            $count_nutrition++;
                            if($yield < $min_massa_dishes){
                                $min_massa_dishes = $yield;
                            }
                            if($yield > $max_massa_dishes){
                                $max_massa_dishes = $yield;
                            }
                        }
                        $total_informations['min_yield'] = $min_massa_dishes;
                        $total_informations['max_yield'] = $max_massa_dishes;
                        $total_informations['yield'] = $total_informations['yield'] + $yield;


                        $menus_dishes = MenusDishes::find()->where(['date_fact_menu' => 0, 'menu_id' => $menu->id, 'cycle' => $i_cycle, 'days_id'=> $day->days_id, 'nutrition_id' => $nutrition])->all();
                        $kkal_zavtrak = 0;
                        $salt_zavtrak = 0;
                        $sahar_zavtrak = 0;
                        $ovoshi = 0; $frukti = 0; $kolbasa = 0;$med=0;$yagoda=0;$konditer = 0;

                        foreach($menus_dishes as $m_dishes){
							$salt_dish = 0;$sahar_dish = 0;
                            $dishes = Dishes::findOne($m_dishes->dishes_id);

                            $dishes_products = DishesProducts::find()->where(['dishes_id' => $m_dishes->dishes_id])->all();
                            $kkal_dish = 0;$vitamin_c=0;$vitamin_b1=0;$vitamin_b2=0;$vitamin_a=0;$ca=0;$p=0;$mg=0;$fe=0;$i=0;$se=0;

                            foreach($dishes_products as $d_product){
                                $sahar = 0; $salt =0;
                                $culinary_processing = Dishes::findOne($m_dishes->dishes_id)->culinary_processing_id;
                                if($d_product->products_id ==213 || $d_product->products_id ==214){
                                    $salt_dish = $salt_dish + $d_product->net_weight*($m_dishes->yield / $dishes->yield);
                                }
                                if($d_product->products_id ==181){
                                    $sahar_dish = $sahar_dish + $d_product->net_weight*($m_dishes->yield / $dishes->yield);
                                    //$sahar_dish = $sahar_dish + ($d_product->net_weight/100) *($m_dishes->yield / $dishes->yield);
                                }

                                $product = Products::findOne($d_product->products_id);
                                if(/*$dishes->dishes_category_id == 12 && */$product->products_category_id == 49){
                                    $med = 1;
                                }
                                if($dishes->dishes_category_id == 12 && $product->products_category_id == 8){
                                    $kolbasa = 1;
                                }
                                if($dishes->dishes_category_id == 12 && $product->products_category_id == 9){
                                    $konditer = 1;
                                    //print_r($menu_ids);exit;
                                }
                                if($dishes->dishes_category_id == 12 && $product->products_category_id == 21){
                                    $ovoshi = 1;
                                }
                                if($dishes->dishes_category_id == 12 && $product->products_category_id == 35){
                                    $frukti = 1;
                                }
                                if($product->products_subcategory_id == 68 && $dishes->dishes_category_id == 6){
                                    $yagoda = 1;
                                }
                                $sum = 0;
                                $uvarka = 1;
                                $dishes_yield = $dishes->yield;
                                if($dishes->yield == 0 || empty($dishes->yield)){
                                    return "null";
                                }
                                if($culinary_processing != 3){
                                    $kkal = ($product->protein * 4 * 0.94) + ($product->fat * 9 * 0.88) + ($product->carbohydrates_total * 4 * 0.91);

                                    $vitamin_c = $vitamin_c + (0.40*$product->vitamin_c * $d_product->net_weight)/100;
                                    $vitamin_b1 = $vitamin_b1 + (0.72*$product->vitamin_b1 * $d_product->net_weight)/100;
                                    $vitamin_b2 = $vitamin_b2 + (0.8*$product->vitamin_b2 * $d_product->net_weight)/100;
                                    $vitamin_a = $vitamin_a + (0.6*$product->vitamin_a * $d_product->net_weight)/100;
                                    $ca = $ca + (0.88*$product->ca * $d_product->net_weight)/100;
                                    $p = $p + (0.87*$product->p * $d_product->net_weight)/100;
                                    $mg = $mg + (0.87*$product->mg * $d_product->net_weight)/100;
                                    $fe = $fe + (0.87*$product->fe * $d_product->net_weight)/100;
                                    $i = $i + (0.87*$product->i * $d_product->net_weight)/100;
                                    $se = $se + (0.88*$product->se * $d_product->net_weight)/100;
                                }
                                else{
                                    $kkal = ($product->protein * 4) + ($product->fat * 9) + ($product->carbohydrates_total * 4);

                                    $vitamin_c = $vitamin_c + ($product->vitamin_c * $d_product->net_weight)/100;
                                    $vitamin_b1 = $vitamin_b1 + ($product->vitamin_b1 * $d_product->net_weight)/100;
                                    $vitamin_b2 = $vitamin_b2 + ($product->vitamin_b2 * $d_product->net_weight)/100;
                                    $vitamin_a = $vitamin_a + ($product->vitamin_a * $d_product->net_weight)/100;
                                    $ca = $ca + ($product->ca * $d_product->net_weight)/100;
                                    $p = $p + ($product->p * $d_product->net_weight)/100;
                                    $mg = $mg + ($product->mg * $d_product->net_weight)/100;
                                    $fe = $fe + ($product->fe * $d_product->net_weight)/100;
                                    $i = $i + ($product->i * $d_product->net_weight)/100;
                                    $se = $se + ($product->se * $d_product->net_weight)/100;
                                }

                                $kkal_product = $kkal * ($d_product->net_weight/100) *($m_dishes->yield / $dishes->yield);
                                $kkal_dish = $kkal_dish + $kkal_product;
                                
                            }

                            $total_informations['vitamin_c'] =  $total_informations['vitamin_c']  + $vitamin_c*($m_dishes->yield/$dishes->yield);
                            $total_informations['vitamin_b1'] = $total_informations['vitamin_b1'] + $vitamin_b1*($m_dishes->yield/$dishes->yield);
                            $total_informations['vitamin_b2'] = $total_informations['vitamin_b2'] + $vitamin_b2*($m_dishes->yield/$dishes->yield);
                            $total_informations['vitamin_a'] =  $total_informations['vitamin_a']  + $vitamin_a*($m_dishes->yield/$dishes->yield);
                            $total_informations['ca'] = $total_informations['ca'] + $ca*($m_dishes->yield/$dishes->yield);
                            $total_informations['p'] =  $total_informations['p']  + $p*($m_dishes->yield/$dishes->yield);
                            $total_informations['mg'] = $total_informations['mg'] + $mg*($m_dishes->yield/$dishes->yield);
                            $total_informations['fe'] = $total_informations['fe'] + $fe*($m_dishes->yield/$dishes->yield);
                            $total_informations['i'] = $total_informations['i'] + $i*($m_dishes->yield/$dishes->yield);
                            $total_informations['se'] = $total_informations['se'] + $se*($m_dishes->yield/$dishes->yield);
                            $kkal_zavtrak = $kkal_zavtrak +$kkal_dish;

                            $total_informations['sahar'] = $total_informations['sahar'] + $sahar_dish;
                            $total_informations['salt'] = $total_informations['salt'] + $salt_dish;
                        }

                        if($kkal_zavtrak < $min_kkal && $kkal_zavtrak >0){
                            $min_kkal = $kkal_zavtrak;
                        }
                        if($kkal_zavtrak > $max_kkal){
                            $max_kkal = $kkal_zavtrak;
                        }
                        $total_informations['kkal'] = $total_informations['kkal'] + $kkal_zavtrak;
                        $total_informations['min_kkal'] = $min_kkal;
                        $total_informations['max_kkal'] = $max_kkal;
                        if($ovoshi == 1){
                            $total_informations['ovoshi'] = $total_informations['ovoshi'] + 1;
                        }
                        if($frukti == 1){
                            $total_informations['frukti'] = $total_informations['frukti'] + 1;
                        }
                        if($kolbasa == 1){
                            $total_informations['kolbasa'] = $total_informations['kolbasa'] + 1;
                        }
                        if($konditer == 1){
                            $total_informations['konditer'] = $total_informations['konditer'] + 1;
                        }
                        if($med == 1){
                            $total_informations['med'] = $total_informations['med'] + 1;
                        }
                        if($yagoda == 1){
                            $total_informations['yagoda'] = $total_informations['yagoda'] + 1;
                        }
                    }
                }
            }
        }


        if($count_nutrition == 0){
            $total_informations['yield'] = 0;
            $total_informations['kkal'] = 0;
        }else{
            $total_informations['yield'] = $total_informations['yield']/$count_nutrition;
            $total_informations['kkal'] = $total_informations['kkal']/$count_nutrition;

            $total_informations['vitamin_c'] = $total_informations['vitamin_c'] /$count_nutrition;
            $total_informations['vitamin_b1']= $total_informations['vitamin_b1']/$count_nutrition;
            $total_informations['vitamin_b2']= $total_informations['vitamin_b2']/$count_nutrition;
            $total_informations['vitamin_a'] = $total_informations['vitamin_a'] /$count_nutrition;
            $total_informations['ca'] = $total_informations['ca']/$count_nutrition;
            $total_informations['p']  = $total_informations['p'] /$count_nutrition;
            $total_informations['mg'] = $total_informations['mg']/$count_nutrition;
            $total_informations['fe'] = $total_informations['fe']/$count_nutrition;
            $total_informations['i'] = $total_informations['i']/$count_nutrition;
            $total_informations['se'] = $total_informations['se']/$count_nutrition;
            $total_informations['salt'] = $total_informations['salt']/$count_nutrition;
            $total_informations['sahar'] = $total_informations['sahar']/$count_nutrition;

            $total_informations['ovoshi'] = $total_informations['ovoshi']/count($menu_ids);
            $total_informations['frukti'] = $total_informations['frukti']/count($menu_ids);
            $total_informations['kolbasa'] = $total_informations['kolbasa']/count($menu_ids);
            $total_informations['konditer'] = $total_informations['konditer']/count($menu_ids);
            $total_informations['med'] = $total_informations['med']/count($menu_ids);
            $total_informations['yagoda'] = $total_informations['yagoda']/count($menu_ids);

        }

        return $total_informations;

    }
	
	public function get_control_information($organization_id, $nutrition)
    {
        $total_informations = [];
        //$total_informations['vnutr'] = $organization_id;
        //return $total_informations;

        $min_procent = 10000;
        $max_procent = 0;
        $count_m = 0;
        $min_ball = 40;
        $max_ball = 0;
        if($nutrition == 1){
            $models = AnketParentControl::find()->where(['organization_id' => $organization_id, 'status' => [2,1], 'smena' => 1, 'peremena' => [1, 2, 3]])->all();
            $total_informations['vnutr'] = AnketParentControl::find()->where(['organization_id' => $organization_id, 'status' => [2,1], 'smena' => 1, 'peremena' => [1, 2, 3]])->count();
        }else{
            $models = AnketParentControl::find()->where(['organization_id' => $organization_id, 'status' => [2,1], 'smena' => 1, 'peremena' => [4, 5, 6, 7, 8]])->orWhere(['organization_id' => $organization_id, 'status' => [2,1], 'smena' => 2, 'peremena' => [1, 2, 3, 4, 5, 6, 7, 8]])->all();
            $total_informations['vnutr'] = AnketParentControl::find()->where(['organization_id' => $organization_id, 'status' => [2,1], 'smena' => 1, 'peremena' => [4, 5, 6, 7, 8]])->orWhere(['organization_id' => $organization_id, 'status' => [2,1], 'smena' => 2, 'peremena' => [1, 2, 3, 4, 5, 6, 7, 8]])->count();
        }

        if(empty($models)){
            return 'null';
        }
        else{

            foreach ($models as $model)
            {
                
                if ($model->masa_porcii > 0 && $model->count > 0)
                {
					$count_m++;
                    $procent = (($model->masa_othodov * 1000) / ($model->masa_porcii * $model->count)) * 100;
                    $total_informations['sred_procent'] = $total_informations['sred_procent'] + $procent;

                    if ($procent < $min_procent)
                    {
                        $min_procent = $procent;
                    }
                    if ($procent > $max_procent)
                    {
                        $max_procent = $procent;
                    }

                    if ($procent <= 20)
                    {
                        $np_itog = 12;
                    }
                    elseif ($procent > 20 && $procent <= 30)
                    {
                        $np_itog = 8;
                    }
                    elseif ($procent > 40 && $procent <= 50)
                    {
                        $np_itog = 3;
                    }
                    elseif ($procent > 50 && $procent <= 60)
                    {
                        $np_itog = 1;
                    }
                    else
                    {
                        $np_itog = 0;
                    }


                    $count = 0;
                    for ($i = 1; $i <= 14; $i++)
                    {
                        $question = 'question' . $i;
                        if ($i == 8 || $i == 9 || $i == 10)
                        {
                            if ($model->$question == 1)
                            {
                                $count = $count + 0;
                            }
                            if ($model->$question == 0)
                            {
                                $count = $count + 2;
                            }
                        }
                        else
                        {
                            if ($model->$question == 1)
                            {
                                $count = $count + 2;
                            }
                            if ($model->$question == 2)
                            {
                                $count = $count + 1;
                            }
                            if ($model->$question == 0)
                            {
                                $count = $count + 0;
                            }
                        }

                    }


                    if (($count + $np_itog) < $min_ball)
                    {
                        $min_ball = $count + $np_itog;
                    }
                    if (($count + $np_itog) > $max_ball)
                    {
                        $max_ball = $count + $np_itog;
                    }

                    $total_informations['sred_ball'] = $total_informations['sred_ball'] + $count + $np_itog;
                }
            }
			/*if(empty($total_informations)){
				return 'null';
			}*/
            if($count_m != 0)
            {
                $total_informations['sred_procent'] = $total_informations['sred_procent'] / $count_m;
                $total_informations['sred_ball'] = $total_informations['sred_ball'] / $count_m;
            }
            else{
                $total_informations['sred_procent'] = 0;
                $total_informations['sred_ball'] = 0;
            }
            $total_informations['min_procent'] = $min_procent;
            $total_informations['max_procent'] = $max_procent;

            $total_informations['min_ball'] = $min_ball;
            $total_informations['max_ball'] = $max_ball;
        }

        return $total_informations;
    }
}
