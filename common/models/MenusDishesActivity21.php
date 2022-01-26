<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "menus_dishes".
 *
 * @property int $id
 * @property int|null $menu_id
 * @property int|null $nutrition_id
 * @property int|null $dishes_id
 * @property float|null $yield
 * @property string|null $created_at
 * @property string|null $updated_at
 */
class MenusDishesActivity21 extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'menus_dishes';
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
            [['menu_id', 'nutrition_id', 'dishes_id'], 'integer'],
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
            'id' => 'ID',
            'menu_id' => 'Menu ID',
            'nutrition_id' => 'Nutrition ID',
            'dishes_id' => 'Dishes ID',
            'yield' => 'Yield',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }


    public function check_sr(){
        $modelMenus = new MenusActivity();
        $modelMenusDishes = new MenusDishesActivity();
        $model_form = new DailyRoutineFormActivity();
        $its_me = UsersActivity::find()->where(['key_login' => Yii::$app->user->identity->key_login])->one();
        $indicator_activity = 0;
        $indicator_menu = 0;
        $my_menu = MenusActivity::find()->where(['user_id' => $its_me->id])->one();
        if(empty($my_menu)){
            return 0;
        }
        if(empty(KidsActivity::find()->where(['user_id' => $its_me->id])->one())){
            return 0;
        }

        $menus_nutritions = MenusNutritionActivity::find()->where(['menu_id' => $my_menu->id])->all();
        $ids = [];
        foreach ($menus_nutritions as $m_nutrition)
        {
            $ids[] = $m_nutrition->nutrition_id;
        }
        $nutritions = NutritionInfoActivity::find()->where(['id' => $ids])->all();


        $second_zavtrak = MenusNutritionActivity::find()->where(['menu_id' => $my_menu->id, 'nutrition_id' => 2])->count();
        $second_uzhin = MenusNutritionActivity::find()->where(['menu_id' => $my_menu->id, 'nutrition_id' => 6])->count();



        $super_total_kkal = $modelMenusDishes->get_super_total_kkal($my_menu->id, 'super_total');
        $energy = $model_form->get_total(DailyRoutineNumberActivity::find()->where(['kids_id' => KidsActivity::find()->where(['user_id' => $its_me->id])->one()['id']])->one()['id'], 'check');
        $minKkal = $energy - ($energy / 100 * 20);
        $maxKkal = $energy + ($energy / 100 * 20);

        if ($super_total_kkal < $minKkal || $super_total_kkal > $maxKkal){
            $indicator_activity = 0;
        }
        else{
            $indicator_activity = 1;
        }

         foreach ($nutritions as $nutrition)
        {
            $kkal_nutrition = 0;
            $indicator = 0;
            if ($nutrition->id == 1 && $second_zavtrak == 0)
            {
                $indicator = NutritionProcentActivity::find()->where(['nutrition_id' => 2])->one()->procent ;
            }
            if ($nutrition->id == 5 && $second_uzhin == 0)
            {
                $indicator = NutritionProcentActivity::find()->where(['nutrition_id' => 6])->one()->procent;
            }
            $norma = round((NutritionProcentActivity::find()->where(['nutrition_id' => $nutrition->id])->one()->procent + $indicator) * $energy / 100);
            $norma_minus = $norma * 0.8;
            $norma_plus = $norma * 1.2;
            $kkal_nutrition = round($modelMenusDishes->get_kkal_nutrition($my_menu->id, $nutrition->id), 1);
            if ($kkal_nutrition > $norma_minus && $kkal_nutrition < $norma_plus)
            {
                $indicator_menu++;
            }

        }
        if($indicator_activity == 1 && $indicator_menu == count($ids)){
            return 1;
        }else{
            return 0;
        }
    }



//function for sr work another users
    public function check_sr_user($user_id){
        $user = User::findOne($user_id);
        $user_key = $user->key_login;
        $modelMenus = new MenusActivity();
        $modelMenusDishes = new MenusDishesActivity();
        $model_form = new DailyRoutineFormActivity();
        $its_me = UsersActivity::find()->where(['key_login' => $user_key])->one();
        $indicator_activity = 0;
        $indicator_menu = 0;
        $my_menu = MenusActivity::find()->where(['user_id' => $its_me->id])->one();
        if(empty($my_menu)){
            return 0;
        }
        if(empty(KidsActivity::find()->where(['user_id' => $its_me->id])->one())){
            return 0;
        }

        $menus_nutritions = MenusNutritionActivity::find()->where(['menu_id' => $my_menu->id])->all();
        $ids = [];
        foreach ($menus_nutritions as $m_nutrition)
        {
            $ids[] = $m_nutrition->nutrition_id;
        }
        $nutritions = NutritionInfoActivity::find()->where(['id' => $ids])->all();


        $second_zavtrak = MenusNutritionActivity::find()->where(['menu_id' => $my_menu->id, 'nutrition_id' => 2])->count();
        $second_uzhin = MenusNutritionActivity::find()->where(['menu_id' => $my_menu->id, 'nutrition_id' => 6])->count();



        $super_total_kkal = $modelMenusDishes->get_super_total_kkal($my_menu->id, 'super_total');
        $energy = $model_form->get_total(DailyRoutineNumberActivity::find()->where(['kids_id' => KidsActivity::find()->where(['user_id' => $its_me->id])->one()['id']])->one()['id'], 'check');
        $minKkal = $energy - ($energy / 100 * 20);
        $maxKkal = $energy + ($energy / 100 * 20);

        if ($super_total_kkal < $minKkal || $super_total_kkal > $maxKkal){
            $indicator_activity = 0;
        }
        else{
            $indicator_activity = 1;
        }

        foreach ($nutritions as $nutrition)
        {
            $kkal_nutrition = 0;
            $indicator = 0;
            if ($nutrition->id == 1 && $second_zavtrak == 0)
            {
                $indicator = NutritionProcentActivity::find()->where(['nutrition_id' => 2])->one()->procent ;
            }
            if ($nutrition->id == 5 && $second_uzhin == 0)
            {
                $indicator = NutritionProcentActivity::find()->where(['nutrition_id' => 6])->one()->procent;
            }
            $norma = round((NutritionProcentActivity::find()->where(['nutrition_id' => $nutrition->id])->one()->procent + $indicator) * $energy / 100);
            $norma_minus = $norma * 0.8;
            $norma_plus = $norma * 1.2;
            $kkal_nutrition = round($modelMenusDishes->get_kkal_nutrition($my_menu->id, $nutrition->id), 1);
            if ($kkal_nutrition > $norma_minus && $kkal_nutrition < $norma_plus)
            {
                $indicator_menu++;
            }

        }
        if($indicator_activity == 1 && $indicator_menu == count($ids)){
            return 1;
        }else{
            return 0;
        }
    }








    public function check_sr_lite($id){
        //если создано меню user_id
        //если в MenuDishes menu_id
        //добавлен ли режим дня
        //retutn пройден
        //else не пройден
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
        $culinary_processing = DishesActivity21::findOne($dishes_id)->culinary_processing_id;
        $product = ProductsActivity21::findOne($id);
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
                $uvarka = 0.75;
            }
            if($field == 'vitamin_b1'){
                $uvarka = 0.25;
            }
            if($field == 'vitamin_c'){
                $uvarka = 0.25;
            }
            else{
                $uvarka = 1;
            }
        }

        foreach($dishes_products as $d_product){
            $product = Products::findOne($d_product->products_id);
            $protein = ($product->$field * $d_product->net_weight)/100;
            $sum = $sum + $protein;
        }
        $sum = $sum*($yield/$dishes_yield) * $uvarka;
        return $sum;
    }


    /*РАСЧЕТ белков/жиров/углеводов для отдельного продукта С КОЕФИЦИЕНТАМИ уварки ИСПОЛЬЗУЕТСЯ ВО ВСЕМ КОНТРОЛЛЕРЕ МЕНЮСДИШЕС*/
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

    //Итого выход за определенный прием пищи
    public function get_total_yield($menu_id, $nutrition_id){
        $menus_dishes = MenusDishes::find()->where(['menu_id' => $menu_id, 'nutrition_id' => $nutrition_id])->all();
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
    public function get_bju_nutrition($menu_id, $nutrition_id, $field){
        $menus_dishes = MenusDishes::find()->where(['menu_id' => $menu_id, 'nutrition_id' => $nutrition_id])->all();
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
    public function get_kkal_nutrition($menu_id, $nutrition_id){
        $menus_dishes = MenusDishesActivity21::find()->where(['menu_id' => $menu_id, 'nutrition_id' => $nutrition_id])->all();
        $total = 0;
        foreach($menus_dishes as $m_dishes){

            $total = $total + $this->get_kkal_dish($m_dishes->id);
        }
        return $total;
    }

    public function get_kkal_dish($id){//за блюдо
        $m_dishes = MenusDishesActivity21::findOne($id);
        $dishes = DishesActivity21::findOne($m_dishes->dishes_id);
        $total = 0;
        $dishes_products = DishesProductsActivity21::find()->where(['dishes_id' => $m_dishes['dishes_id']])->all();
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
    public function get_bju($menu_id, $nutrition_id, $field){
        $my_field = $this->get_bju_nutrition($menu_id, $nutrition_id, $field);
        $protein = $this->get_bju_nutrition($menu_id, $nutrition_id, 'protein');
        if($protein != 0){
            $total = ($my_field/$protein);
        }
        return round($total, 2);
    }


    //Процент от общей массы пищевых веществ
    public function get_procent($menu_id, $nutrition_id, $field){
        $total = 0;
        if($field == 'protein'){
            $protein = $this->get_bju_nutrition($menu_id, $nutrition_id, $field);
            $indicator = $protein;
        }
        else{
            $protein = $this->get_bju_nutrition($menu_id, $nutrition_id, 'protein');
        }
        if($field == 'fat'){
            $fat = $this->get_bju_nutrition($menu_id, $nutrition_id, $field);
            $indicator = $fat;
        }
        else{
            $fat = $this->get_bju_nutrition($menu_id, $nutrition_id, 'fat');
        }
        if($field == 'carbohydrates_total'){
            $carbohydrates_total = $this->get_bju_nutrition($menu_id, $nutrition_id, $field);
            $indicator = $carbohydrates_total;
        }
        else{
            $carbohydrates_total = $this->get_bju_nutrition($menu_id, $nutrition_id, 'carbohydrates_total');
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
        $procent = ($this->get_total_yield($menu_id, $cycle, $days_id, $nutrition_id)/$total)*100;

        return round($procent, 1);
    }


    /*процент от суток с учетом уварок*/
    public function get_super_total_field($menu_id, $nutrition_id, $field){
        $menus_dishes = MenusDishes::find()->where(['menu_id' => $menu_id,])->all();
        $menus_nutritions = MenusNutrition::find()->where(['menu_id'=>$menu_id])->all();
        $total = 0;
        foreach($menus_nutritions as $m_nutrition){
            $total = $total + $this->get_bju_nutrition($menu_id, $m_nutrition->nutrition_id, $field);
        }
        if($nutrition_id == 'super_total'){
            return $total;
        }
        $procent = ($this->get_bju_nutrition($menu_id, $nutrition_id, $field)/$total)*100;
        return round($procent, 1);
    }



    public function get_super_total_kkal($menu_id, $nutrition_id){
        //$menus_dishes = MenusDishesActivity::find()->where(['menu_id' => $menu_id])->all();
        $menus_nutritions = MenusNutritionActivity21::find()->where(['menu_id'=>$menu_id])->all();
        $total = 0;
        if (!empty($menus_nutritions))
        {
            foreach($menus_nutritions as $m_nutrition){
                $total = $total + $this->get_kkal_nutrition($menu_id, $m_nutrition->nutrition_id);
            }
            if($nutrition_id == 'super_total'){
                return $total;
            }
            $procent = ($this->get_kkal_nutrition($menu_id, $nutrition_id)/$total)*100;
            return round($procent, 1);
        }
        else{
            return 0;
        }

    }


    public function get_super_total_vitamin($menu_id, $cycle, $days_id, $field){
        $menus_dishes = MenusDishes::find()->where(['date_fact_menu' => 0, 'menu_id' => $menu_id, 'cycle' => $cycle, 'days_id'=> $days_id])->all();
        $menus_nutritions = MenusNutrition::find()->where(['menu_id'=>$menu_id])->all();
        $total = 0;
        foreach($menus_nutritions as $m_nutrition){
            $total = $total + $this->get_total_vitamin($menu_id, $cycle, $days_id, $m_nutrition->nutrition_id, $field);
        }
        return $total;

    }
    /*соотношение бжу за 1 день*/
    public function get_super_total_bju($menu_id, $nutrition_id, $field){
        $my_field = $this->get_super_total_field($menu_id, $nutrition_id, $field);
        $protein = $this->get_super_total_field($menu_id, $nutrition_id, 'protein');
        if($protein != 0){
            $total = ($my_field/$protein)*100;
        }
        return round($total, 1);
    }


    public function get_recommended_normativ($menu_id, $nutrition_id, $field){
        /*$my_menus = Menus::findOne($menu_id);
        $age_id = $my_menus->feeders_characters_id;
        $normativ = NormativInfo::find()->where(['age_info_id' => $age_id, 'nutrition_info_id' => $nutrition_id])->one();*/
        //return $normativ->$field;
        return 1;
    }

    public function get_recommended_normativ_of_day($menu_id, $field){
        $my_menus = Menus::findOne($menu_id);
        $age_id = $my_menus->feeders_characters_id;
        $normativ = NormativInfo::find()->where(['age_info_id' => $age_id, 'nutrition_info_id' => 0])->one();
        return $normativ->$field;
        //return 1;
    }

    public function get_techmup($menu_id){
        $my_menus = Dishes::findOne($menu_id);
        return $my_menus->techmup_number;
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

    public function get_total_raskladka_yield($menu_dishes){
        //$menu_dishes = 411;
        $informations = DishesProducts::find()->
        select(['menus_dishes.id as unique','dishes_products.dishes_id','dishes_products.products_id', 'dishes_products.net_weight', 'dishes_products.gross_weight', 'dishes.yield as dishes_yield', 'menus_dishes.cycle as cycle', 'menus_dishes.nutrition_id', 'menus_dishes.days_id as days_id', 'menus_dishes.yield as menus_yield', 'menus_dishes.menu_id as menu_id'])->
        leftJoin('menus_dishes', 'dishes_products.dishes_id = menus_dishes.dishes_id')->
        leftJoin('dishes', 'dishes_products.dishes_id = dishes.id')->
        where(['menus_dishes.date_fact_menu' => '0','menus_dishes.id' => $menu_dishes])->
        asArray()->
        all();

        return $informations;

    }
}
