<?php

namespace common\models;

use DateTime;
use Yii;
use yii\base\Model;

class Report extends Model
{
    public $report_federal_district;

    public function time_sleep($time_start, $time_end)
    {
        $start_date = new DateTime('2021-05-02' . $time_end);
        $since_start = $start_date->diff(new DateTime('2021-05-03' . $time_start));
        $sleep_min = $since_start->i + ($since_start->h * 60); //время сна в минутах
        return $sleep_min;
    }

    public function time_school($time_start, $time_end)
    {
        $start_date2 = new DateTime('2021-05-02 ' . $time_start);
        $since_start2 = $start_date2->diff(new DateTime('2021-05-03 ' . $time_end));
        $school_min = $since_start2->i + ($since_start2->h * 60); //время нахождения в школе в минутах
        return $school_min;
    }

    /* Расчёт для самостоятельной по обучению и г. Казань */
    public function imt(
        $mass,
        $height,
        $sex,
        $age,
        $sleep_start,
        $sleep_end,
        $sleep_day,
        $sleep_day_time,
        $charging,
        $charging_time,
        $walk,
        $walk_time,
        $additional_education,
        $additional_education_time,
        $sports_section,
        $sports_section1,
        $sports_section1_time,
        $sports_section2,
        $sports_section2_time,
        $travel_time_foot,
        $travel_time_transport,
        $school_start,
        $school_end,
        $use_telephone,
        /*Меню*/
        $menu_id
    ) {
        if (!$mass || !$height) {
            $queteletIndexVal = '';
            $queteletIndexText = '';
            return array($queteletIndexVal, $queteletIndexText);
        }

        /* Двигательная активность */
        $queteletIndexVal = $mass / (pow($height / 100, 2)); // ИМТ (число)

        /* Физическое развитие */
        $imt_arrayVal = [
            'дефицит массы тела',
            'гармоничное', // Нормальный вес
            'избыток массы тела',
            'ожирение'
        ];
        $imt_boys = [
            0 => [14.5, 18.1, 18.5],
            1 => [14.5, 18.1, 18.5],
            2 => [14.5, 18.1, 18.5],
            3 => [14, 17.5, 17.8],
            4 => [13.9, 17, 17.3],
            5 => [13.8, 16.9, 17.2],
            6 => [13.5, 17, 17.5],
            7 => [13.6, 17.5, 18],
            8 => [13.8, 18, 18.5],
            9 => [13.9, 18.5, 19.5],
            10 => [14, 19.2, 20.4],
            11 => [14.3, 20, 21.3],
            12 => [14.7, 21, 22.1],
            13 => [15.1, 21.8, 23],
            14 => [15.6, 22.5, 23.9],
            15 => [16.3, 23.5, 24.7],
            16 => [16.9, 24, 25.4],
            17 => [17.3, 25, 26.1],
            18 => [17.9, 25.6, 26.9],
            19 => [18.2, 26.2, 27.8],
            20 => [18.6, 27, 28.4],
            21 => [18.6, 27, 28.4],
            22 => [18.6, 27, 28.4],
            23 => [18.6, 27, 28.4],
            24 => [18.6, 27, 28.4],
            25 => [18.6, 27, 28.4],
            26 => [18.6, 27, 28.4]
        ];
        $imt_girls = [
            0 => [14, 18, 18.2],
            1 => [14, 18, 18.2],
            2 => [14, 18, 18.2],
            3 => [13.8, 17.2, 17.4],
            4 => [13.5, 16.8, 17.1],
            5 => [13.3, 16.9, 17.2],
            6 => [13.2, 17, 17.3],
            7 => [13.2, 17.9, 18.4],
            8 => [13.2, 18.5, 18.7],
            9 => [13.5, 19, 19.6],
            10 => [13.9, 20, 21],
            11 => [14, 21, 22],
            12 => [14.5, 21.6, 23],
            13 => [15, 22.5, 24],
            14 => [15.5, 23.5, 24.8],
            15 => [16, 24, 25.5],
            16 => [16.5, 24.8, 26],
            17 => [16.9, 25.1, 26.8],
            18 => [17, 25.8, 27.3],
            19 => [17.2, 25.8, 27.8],
            20 => [17.5, 25.5, 28.2],
            21 => [17.5, 25.5, 28.2],
            22 => [17.5, 25.5, 28.2],
            23 => [17.5, 25.5, 28.2],
            24 => [17.5, 25.5, 28.2],
            25 => [17.5, 25.5, 28.2],
            26 => [17.5, 25.5, 28.2]
        ];
        $recommended_value_boys = [
            0 => [110, 59.4, 8.9, 41.7],
            1 => [110, 59.4, 8.9, 41.7],
            2 => [88.2, 47.6, 7.1, 33.4],
            3 => [88.9, 48.0, 7.2, 33.7],
            4 => [96.0, 51.9, 7.8, 36.4],
            5 => [90.5, 48.8, 7.3, 34.3],
            6 => [85.2, 46.0, 6.9, 32.3],
            7 => [80.5, 43.5, 6.5, 30.5],
            8 => [74.1, 40.0, 6.0, 28.1],
            9 => [69.9, 37.8, 5.7, 26.5],
            10 => [65.7, 35.5, 5.3, 24.9],
            11 => [64.9, 35.0, 5.3, 24.6],
            12 => [61.9, 33.4, 5.0, 23.5],
            13 => [55.9, 30.2, 4.5, 21.2],
            14 => [51.1, 27.6, 4.1, 19.4],
            15 => [49.0, 26.4, 4.0, 18.6],
            16 => [47.4, 25.6, 3.8, 18.0],
            17 => [46.3, 25.0, 3.8, 17.5],
            18 => [46.3, 25.0, 3.8, 17.5],
            19 => [46.3, 25.0, 3.8, 17.5],
            20 => [46.3, 25.0, 3.8, 17.5],
        ];
        $recommended_value_girls = [
            0 => [110.526, 59.7, 9.0, 41.9],
            1 => [110.526, 59.7, 9.0, 41.9],
            2 => [91.954, 49.7, 7.4, 34.9],
            3 => [90.615, 48.9, 7.3, 34.3],
            4 => [96.045, 51.9, 7.8, 36.4],
            5 => [91.371, 49.3, 7.4, 34.6],
            6 => [86.560, 46.7, 7.0, 32.8],
            7 => [81.301, 43.9, 6.6, 30.8],
            8 => [74.275, 40.1, 6.0, 28.2],
            9 => [68.8, 37.2, 5.6, 26.1],
            10 => [66.066, 35.7, 5.4, 25.0],
            11 => [59.459, 32.1, 4.8, 22.5],
            12 => [56.312, 30.4, 4.6, 21.3],
            13 => [47.938, 25.9, 3.9, 18.2],
            14 => [46.332, 25.0, 3.8, 17.6],
            15 => [46.226, 25.0, 3.7, 17.5],
            16 => [45.455, 24.5, 3.7, 17.2],
            17 => [45.213, 24.4, 3.7, 17.1],
            18 => [45.213, 24.4, 3.7, 17.1],
            19 => [45.213, 24.4, 3.7, 17.1],
            20 => [45.213, 24.4, 3.7, 17.1]
        ];

        if ($sex == 0) {
            $BoysOrGirls = $imt_girls;
            $calculationRecValue = $recommended_value_girls[$age];
        } elseif ($sex == 1) {
            $BoysOrGirls = $imt_boys;
            $calculationRecValue = $recommended_value_boys[$age];
        } else {
            print_r(
                'Ошибка (IWC-ADR #1). Не выбран пол ребёнка в общей информации. Если после заполнения ошибка осталась - напишите на edu@niig.su приложив данную ошибку и свой идентификационный номер.'
            );;
            die();
        }

        if ($queteletIndexVal < $BoysOrGirls[$age][0]) {
            $arrayChildNum = 0;
        } elseif ($queteletIndexVal >= $BoysOrGirls[$age][0] && $queteletIndexVal <= $BoysOrGirls[$age][1]) {
            $arrayChildNum = 1;
        } elseif ($queteletIndexVal > $BoysOrGirls[$age][1] && $queteletIndexVal <= $BoysOrGirls[$age][2]) {
            $arrayChildNum = 2;
        } elseif ($queteletIndexVal > $BoysOrGirls[$age][2]) {
            $arrayChildNum = 3;
        }

        $queteletIndexText = $imt_arrayVal[$arrayChildNum]; #физическое развитие словами с учётом возраста

        /**/
        $totalEnergy_rec = $mass * $calculationRecValue[0]; #рекомендуюмые энерготраты для ребенка (масса ребёнка * )
        $OO = $totalEnergy_rec * 0.54; #основной обмен (фактический = рекомендуемому)
        $SDDP = $OO * 0.16; #СДДП (фактический = рекомендуемому)

        /* Расчёт энерготрат на физическую активность*/
        $unrecordedTime = 1440; #неучтенное время
        $timeStreet = 0; #время нахождения на улице

        $start_date = new DateTime('2021-05-02' . $sleep_end);
        $since_start = $start_date->diff(new DateTime('2021-05-03' . $sleep_start));
        $sleep_min = $since_start->i + ($since_start->h * 60); #время сна в минутах
        //print_r($sleep_min);die();

        $total_sleep = $sleep_min; #общее время сна в минутах за день
        if ($sleep_day == 1) #если спал днем, то добавляем время дневного сна
        {
            $total_sleep = $sleep_min + $sleep_day_time; //общее время сна в минутах за день
        }
        $unrecordedTime = $unrecordedTime - $total_sleep;

        /*расчет энерготрат на учтенную физическую активность*/
        $DA = 0; #Энерготраты на двигательную активность за день
        if ($charging == 1) #если была зарядка
        {
            $DA += $charging_time * 0.061;
            $unrecordedTime = $unrecordedTime - $charging_time;
        }

        if ($walk == 1) #если гуляли в этот день
        {
            $DA += $walk_time * 0.061;
            $unrecordedTime = $unrecordedTime - $walk_time;
            $timeStreet = $timeStreet + $walk_time;
        }
        if ($additional_education == 1) #если занимались ли в кружках доп. образования
        {
            $DA += $additional_education_time * 0.0220;
            $unrecordedTime = $unrecordedTime - $additional_education_time;
        }
        if ($sports_section == 1) #занимались ли в спорт секциях
        {
            $sport_section1 = SportsSectionName::findOne($sports_section1)->val;
            $DA += $sports_section1_time * $sport_section1;
            $unrecordedTime = $unrecordedTime - $sports_section1_time;
            if (!empty($sports_section2)) {
                $sport_section2 = SportsSectionName::findOne($sports_section2)->val;
                $DA += $sports_section2_time * $sport_section2;
                $unrecordedTime = $unrecordedTime - $sports_section2_time;
            }
        }
        #$DA_rec = $totalEnergy_rec - $SDDP - $OO; #двигательная активность рекомендуемая

        $DA = $DA + (($travel_time_foot) * 0.061); #добавление в пути в школу и из нее пешком + секции
        $unrecordedTime = $unrecordedTime - $travel_time_foot;
        #$timeStreet = $timeStreet + $travel_time_foot;
        $DA = $DA + (($travel_time_transport) * 0.015); #добавление в пути в школу и из нее на транспорте + секции
        $unrecordedTime = $unrecordedTime - $travel_time_transport;
        #$timeStreet = $timeStreet + $travel_time_transport;
        $DA = $DA * $mass;

        $start_date2 = new DateTime('2021-05-02 ' . $school_start);
        $since_start2 = $start_date2->diff(new DateTime('2021-05-03 ' . $school_end));
        $school_min = $since_start2->i + ($since_start2->h * 60); //время нахождения в школе в минутах

        $unrecordedTime = $unrecordedTime - $school_min;

        if ($use_telephone == 0)#расчет при учете неиспользования сотового телефона
        {
            #75% с умственной нагрузкой
            #25% с умеренная двигательная активность
            $DA_temp = (($school_min * 0.75 * 0.022) + ($school_min * 0.25 * 0.061)) * $mass;
        } elseif ($use_telephone == 1) #расчет при учете использования сотового телефона
        {
            #75% с умственной нагрузкой
            #15% без умственной нагрузкой
            #5% в положении стоя
            #5% с умеренная двигательная активность
            $DA_temp = (($school_min * 0.75 * 0.022) + ($school_min * 0.15 * 0.015) + ($school_min * 0.05 * 0.0397) + ($school_min * 0.05 * 0.061)) * $mass;
        } else {
            print_r('Ошибка определения использования телефона в школе. Напишите на edu@niig.su');
            die();
        }

        $DA += $DA_temp;
        /**/

        /*Неучтенное время*/
        if ($unrecordedTime > 0) #если есть неучтенное время
        {
            //25% в положении лежа
            //25% в положении стоя
            //20% в положении сидя без умственной нагрузки
            //30% в положении сидя с умственной нагрузкой
            $DA_temp2 = (($unrecordedTime * 0.25 * 0.01) + ($unrecordedTime * 0.25 * 0.0397) + ($unrecordedTime * 0.2 * 0.015) + ($unrecordedTime * 0.3 * 0.022)) * $mass;
        }

        $DA += $DA_temp2;
        $totalEnergy = $DA + $SDDP + $OO; #энерготраты за сутки
        /**/

        $totalEnergy_rec = $mass * $calculationRecValue[0]; #Расчет рекомендуемых энерготрат
        $totalEnergyPlus = $totalEnergy_rec + ($totalEnergy_rec / 100 * 10); #рекомендуемые энерготраты + 10%
        $totalEnergyMinus = $totalEnergy_rec - ($totalEnergy_rec / 100 * 10); #рекомендуемые энерготраты - 10%

        if ($totalEnergy >= $totalEnergyMinus && $totalEnergy <= $totalEnergyPlus) {
            $totalEnergy_comparison = 'соответствуют';
        } //Суточные энерготраты в сравнении с рекомендуемыми (выше, ниже, соответствуют)
        elseif ($totalEnergyPlus < $totalEnergy) {
            $totalEnergy_comparison = 'выше';
        } elseif ($totalEnergyMinus > $totalEnergy) {
            $totalEnergy_comparison = 'ниже';
        } else {
            $totalEnergy_comparison = 'неопределено';
        }
        $totalEnergy_comparisonProcent = 100 / ($totalEnergy_rec / $totalEnergy) - 100;

        $OO_procent = 100 / ($totalEnergy / $OO); //в %
        $SDDP_procent = 100 / ($totalEnergy / $SDDP); //в %
        $DA_procent = 100 / ($totalEnergy / $DA); //в %
        $totalEnergy_procent = 100; //в %
        /* Двигательная активность (END) */

        return array(
            0 => round($queteletIndexVal, 1), #ИМТ
            1 => $queteletIndexText, #Физическое развитие
            2 => round($OO, 1), #Основной обмен
            3 => round($SDDP, 1), #специфически-динамическое действие пищи
            4 => round($DA, 1), #Двигательная активность
            5 => round($totalEnergy, 1), #энерготраты за день

            6 => round($totalEnergy_procent, 1), #энерготраты за день (%)
            7 => round($OO_procent, 1), #Основной обмен (%)
            8 => round($SDDP_procent, 1), #специфически-динамическое действие пищи (%)
            9 => round($DA_procent, 1), #Двигательная активность (%)

            10 => round($totalEnergy_rec, 1), #Рекомендуемые энерготраты за день
            11 => $totalEnergy_comparison, #Суточные энерготраты в сравнении с рекомендуемыми
            12 => round($totalEnergy_comparisonProcent, 1), #Суточные энерготраты в сравнении с рекомендуемыми в %

            13 => 'не определено',
            14 => 'не определено',
            15 => 'не определено',
            16 => 'не определено',
            17 => 'не определено',
            18 => 'не определено',
            19 => 'не определено',
            20 => 'не определено',

            21 => 'не определено',
            22 => 'не определено',
            23 => 'не определено',
            24 => 'не определено',
            25 => 'не определено',
            26 => 'не определено',
            27 => 'не определено',
            28 => 'не определено',
            29 => 'не определено',
            30 => 'не определено',
            31 => 'не определено',
            32 => 'не определено',
            33 => 'не определено',
            34 => 'не определено',
            35 => 'не определено',
            36 => 'не определено',
            37 => 'не определено',
            38 => 'не определено',
            39 => 'не определено',
            40 => 'не определено',
            41 => 'не определено',
            42 => 'не определено',
            43 => 'не определено',
            44 => 'не определено',
            45 => 'не определено',
            46 => 'не определено',
            47 => 'не определено',
            48 => 'не определено',
            49 => 'не определено', #соотвествие калорийности пищи суточным энерготратам (выше, ниже, соответствуют)
            50 => 'не определено',  #соотвествие калорийности пищи суточным энерготратам (в %)
            51 => 'не определено',
            52 => 'не определено',
        );
    }

    public function get_product_protein_vitamin_mineral_kkal($products_id, $culinary_processing_id)
    {
        $product = Products::findOne($products_id);

        if ($culinary_processing_id != 3) {
            $protein = $product->protein * 0.94;
            $kkal = ($product->protein * 4 * 0.94) + ($product->fat * 9 * 0.88) + ($product->carbohydrates_total * 4 * 0.91);

            $vitamin = $product->vitamin_b1 * 0.72 + $product->vitamin_b2 * 0.8 + $product->vitamin_pp * 0.8 + $product->vitamin_c * 0.40 + $product->vitamin_d / 1000 +
                $product->na * 0.76 + $product->k * 0.83 + $product->ca * 0.88 + $product->mg * 0.87 + $product->p * 0.87 + $product->fe / 1000 * 0.87 + $product->i / 1000 + $product->se / 1000 * 0.88 + $product->f;
        } else {
            $protein = $product->protein;
            $kkal = ($product->protein * 4) + ($product->fat * 9) + ($product->carbohydrates_total * 4);
            $vitamin = $product->vitamin_b1 + $product->vitamin_b2 + $product->vitamin_pp + $product->vitamin_c + $product->vitamin_d / 1000 +
                $product->na + $product->k + $product->ca + $product->mg + $product->p + $product->fe / 1000 + $product->i / 1000 + $product->se / 1000 + $product->f;
        }

        return array(
            $protein,
            $kkal,
            $vitamin
        );
    }

    public function get_dish_protein_vitamin_mineral_kall($dishes_id, $yield)
    {
        $dishes = Dishes::findOne($dishes_id);
        $dishes_products = DishesProducts::find()->where(['dishes_id' => $dishes_id])->all();

        $total_protein = 0;
        $total_kkal = 0;
        $total_vitamin = 0;
        foreach ($dishes_products as $d_product) {
            $arr_data = $this->get_product_protein_vitamin_mineral_kkal(
                $d_product->products_id,
                $dishes->culinary_processing_id
            );
            $total_protein = $total_protein + $arr_data[0] * ($d_product->net_weight / 100) * ($yield / $dishes->yield);
            $total_kkal = $total_kkal + $arr_data[1] * ($d_product->net_weight / 100) * ($yield / $dishes->yield);
            $total_vitamin = $total_vitamin + $arr_data[2] * ($d_product->net_weight / 100);
        }

        return array(
            $total_protein,
            $total_kkal,
            $total_vitamin,
        );
    }

    public function get_day_protein_vitamin_mineral($menu_id, $age, $total_energy)
    {
        $menusDishes = MenusDishes::findAll(['menu_id' => $menu_id]); //все блюда из меню

        $total_kkal_home = 0;
        $total_kkal_school = 0;
        $total_kkal_street = 0;
        $protein_home = 0;
        $protein_school = 0;
        $protein_street = 0;
        $vitamin_home = 0;
        $vitamin_school = 0;
        $vitamin_street = 0;
        $yieldTotal = [];
        $yield = []; #0-дома, 1-школа, 2-иное
        foreach ($menusDishes as $menusDish) {
            $yieldTotal['total'] = $yieldTotal['total'] + $menusDish->yield;
            $yield[$menusDish->type_food] = $yield[$menusDish->type_food] + $menusDish->yield;

            switch ($menusDish->type_food) { //кол-во белка в приеме пищи
                case 0: //дом
                    $arr_data = $this->get_dish_protein_vitamin_mineral_kall($menusDish->dishes_id, $menusDish->yield);
                    $protein_home = $protein_home + $arr_data[0];
                    $total_kkal_home = $total_kkal_home + $arr_data[1]; //калорийность дома
                    $vitamin_home = $vitamin_home + $arr_data[2]; //витамины дома
                    break;
                case 1: //школа
                    $arr_data = $this->get_dish_protein_vitamin_mineral_kall($menusDish->dishes_id, $menusDish->yield);
                    $protein_school = $protein_school + $arr_data[0];
                    $total_kkal_school = $total_kkal_school + $arr_data[1]; //калорийность в школе
                    $vitamin_school = $vitamin_school + $arr_data[2]; //витамины в школе
                    break;
                case 2: //улица|иное
                    $arr_data = $this->get_dish_protein_vitamin_mineral_kall($menusDish->dishes_id, $menusDish->yield);
                    $protein_street = $protein_street + $arr_data[0];
                    $total_kkal_street = $total_kkal_street + $arr_data[1]; //калорийность на улице|иное
                    $vitamin_street = $vitamin_street + $arr_data[2]; //витамины на улице|иное
                    break;
            }
        }
        $total_kkal = $total_kkal_home + $total_kkal_school + $total_kkal_street; //калорийность меню за день
        //print_r($yield[0]);die();
        //$total_vitamin = $vitamin_home+$vitamin_school+$vitamin_street; //витамины за день

        $yield[4] = isset($yield[0]) ? 100 / ($yieldTotal['total'] / $yield[0]) : 0; #удельный вес массы пищи съеденной за день (в%) - в школе
        $yield[5] = isset($yield[1]) ? 100 / ($yieldTotal['total'] / $yield[1]) : 0; #удельный вес массы пищи съеденной за день (в%) - дома
        $yield[6] = isset($yield[2]) ? 100 / ($yieldTotal['total'] / $yield[2]) : 0; #удельный вес массы пищи съеденной за день (в%) - на улице
        $yield[3] = $yield[4] + $yield[5] + $yield[6]; #удельный вес массы пищи съеденной за день (в%) - всего
        $protein_total = $protein_home + $protein_school + $protein_street;

        $protein_home_procent = empty(!$total_kkal) ? $protein_home * 4 / $total_kkal * 100 : 0; #Удельный вес белков в общей калорийности (в%)
        $protein_school_procent = empty(!$total_kkal) ? $protein_school * 4 / $total_kkal * 100 : 0; #Удельный вес белков в общей калорийности (в%)
        $protein_street_procent = empty(!$total_kkal) ? $protein_street * 4 / $total_kkal * 100 : 0; #Удельный вес белков в общей калорийности (в%)
        $procent_total = $protein_home_procent + $protein_school_procent + $protein_street_procent; #Удельный вес белков в общей калорийности (в%) всего

        $calorieOneKgFood_total = empty(!($yield[0] + $yield[1] + $yield[2])) ? $total_kkal / ($yield[0] + $yield[1] + $yield[2]) * 1000 : 0; #калорийность 1 кг съеденной пищи (ккал)
        $calorieOneKgFood_home = empty(!$yield[0]) ? $total_kkal_home / $yield[0] * 1000 : 0; #калорийность 1 кг съеденной пищи (ккал)
        $calorieOneKgFood_school = empty(!$yield[1]) ? $total_kkal_school / $yield[1] * 1000 : 0; #калорийность 1 кг съеденной пищи (ккал)
        $calorieOneKgFood_street = empty(!$yield[2]) ? $total_kkal_street / $yield[2] * 1000 : 0; #калорийность 1 кг съеденной пищи (ккал)

        $calorieOneKgFood_home_procent = empty(!$calorieOneKgFood_home) ? 100 / ($calorieOneKgFood_total / $calorieOneKgFood_home) : 0; #удельный вес калорийности съеденной пищи (%)
        $calorieOneKgFood_school_procent = empty(!$calorieOneKgFood_school) ? 100 / ($calorieOneKgFood_total / $calorieOneKgFood_school) : 0; #удельный вес калорийности съеденной пищи (%)
        $calorieOneKgFood_street_procent = empty(!$calorieOneKgFood_street) ? 100 / ($calorieOneKgFood_total / $calorieOneKgFood_street) : 0; #удельный вес калорийности съеденной пищи (%)
        $calorieOneKgFood_total_procent = $calorieOneKgFood_home_procent + $calorieOneKgFood_school_procent + $calorieOneKgFood_street_procent; #удельный вес калорийности съеденной пищи (%)

        $proteinOnekg_home_procent = empty(!$calorieOneKgFood_home) ? $protein_home * 4 / $calorieOneKgFood_home * 100 : 0; #Удельный вес белков на 1 кг (в%)
        $proteinOnekg_school_procent = empty(!$calorieOneKgFood_school) ? $protein_school * 4 / $calorieOneKgFood_school * 100 : 0; #Удельный вес белков на 1 кг (в%)
        $proteinOnekg_street_procent = empty(!$calorieOneKgFood_street) ? $protein_street * 4 / $calorieOneKgFood_street * 100 : 0; #Удельный вес белков на 1 кг (в%)
        $procentOnekg_total_procent = $proteinOnekg_home_procent + $proteinOnekg_school_procent + $proteinOnekg_street_procent; #Удельный вес белков на 1 кг (в%)

        $total_vitaminOneKg = ($vitamin_home + $vitamin_school + $vitamin_street) * 1000 / ($yield[0] + $yield[1] + $yield[2]);//витамины*1000/вес #Содержание (суммарное витаминов и минералов) на 1 кг пищи (мг)
        $vitaminOneKg_home = empty(!$yield[0]) ? $vitamin_home * 1000 / $yield[0] : 0; //витамины*1000/вес #Содержание (суммарное витаминов и минералов) на 1 кг пищи (мг)
        $vitaminOneKg_school = empty(!$yield[1]) ? $vitamin_school * 1000 / $yield[1] : 0; //витамины*1000/вес #Содержание (суммарное витаминов и минералов) на 1 кг пищи (мг)
        $vitaminOneKg_street = empty(!$yield[2]) ? $vitamin_street * 1000 / $yield[2] : 0; //витамины*1000/вес #Содержание (суммарное витаминов и минералов) на 1 кг пищи (мг)

        $total_kkal_minus = $total_kkal - ($total_kkal / 100 * 10); //калорийность -10%
        $total_kkal_plus = $total_kkal + ($total_kkal / 100 * 10); //калорийность +10%
        if ($total_kkal_minus <= $total_energy && $total_energy <= $total_kkal_plus) {
            $compliance_caloric_daily_energy = 'соответствуют';
        } #оответствие калорийности пищи суточным энерготратам (выше, ниже, соответствуют)
        elseif ($total_kkal_plus > $total_energy) {
            $compliance_caloric_daily_energy = 'выше';
        } #оответствие калорийности пищи суточным энерготратам (выше, ниже, соответствуют)
        elseif ($total_kkal_minus < $total_energy) {
            $compliance_caloric_daily_energy = 'ниже';
        } #оответствие калорийности пищи суточным энерготратам (выше, ниже, соответствуют)
        else {
            $compliance_caloric_daily_energy = 'неопределено';
        } #оответствие калорийности пищи суточным энерготратам (выше, ниже, соответствуют)

        if ($age >= 0 && $age <= 2) {
            //$age_info = 2;
            $normativ = 1400;
        } elseif ($age >= 3 && $age <= 6) {
            //$age_info = 5;
            $normativ = 1800;
        } elseif ($age >= 7 && $age <= 10) {
            //$age_info = 6;
            $normativ = 2350;
        } elseif ($age >= 11 && $age < 20) {
            //$age_info = 7;
            $normativ = 2713;
        } else {
            $normativ = 'не определено';
        }

        $normativMinus = $normativ - ($normativ / 100 * 10); #норматив калорийности -10%
        $normativPlus = $normativ + ($normativ / 100 * 10);#норматив калорийности +10%
        if ($normativMinus <= $total_kkal && $total_kkal <= $normativPlus) {
            $complianceCaloricDay = 'соответствуют';
        } #оответствие калорийности пищи возрастным нормативам (выше, ниже, соответствуют)
        elseif ($normativPlus < $total_kkal) {
            $complianceCaloricDay = 'выше';
        } #оответствие калорийности пищи возрастным нормативам (выше, ниже, соответствуют)
        elseif ($normativMinus > $total_kkal) {
            $complianceCaloricDay = 'ниже';
        } #оответствие калорийности пищи возрастным нормативам (выше, ниже, соответствуют)
        else {
            $complianceCaloricDay = 'неопределено';
        } #оответствие калорийности пищи возрастным нормативам (выше, ниже, соответствуют)

        $complianceCaloricDay_procent = ($total_kkal != 0) ? ($total_kkal / $normativ * 100) - 100 : 0; #% не соотвествия (+/-)

        return array(
            0 => $yieldTotal['total'], #Масса съеденной за день пищи в граммах
            1 => $yield[0], #Масса съеденной за день пищи в граммах дома
            2 => $yield[1], #Масса съеденной за день пищи в граммах в школе/дет.саду
            3 => $yield[2], #Масса съеденной за день пищи в граммах в ином месте
            4 => $yield[3], #удельный вес массы пищи съеденной за день (в%) - всего
            5 => round($yield[4], 1), #удельный вес массы пищи съеденной за день (в%) - в школе
            6 => round($yield[5], 1), #удельный вес массы пищи съеденной за день (в%) - дома
            7 => round($yield[6], 1), #удельный вес массы пищи съеденной за день (в%) - на улице

            8 => round($protein_total, 1),
            9 => round($protein_home, 1),
            10 => round($protein_school, 1),
            11 => round($protein_street, 1),

            12 => round($procent_total, 1),
            13 => round($protein_home_procent, 1),
            14 => round($protein_school_procent, 1),
            15 => round($protein_street_procent, 1),

            16 => round($total_vitaminOneKg, 1),
            17 => round($vitaminOneKg_school, 1),
            18 => round($vitaminOneKg_home, 1),
            19 => round($vitaminOneKg_street, 1),
            20 => round($calorieOneKgFood_total, 1),
            21 => round($calorieOneKgFood_school, 1),
            22 => round($calorieOneKgFood_home, 1),
            23 => round($calorieOneKgFood_street, 1),
            24 => round($calorieOneKgFood_total_procent, 1),
            25 => round($calorieOneKgFood_school_procent, 1),
            26 => round($calorieOneKgFood_home_procent, 1),
            27 => round($calorieOneKgFood_street_procent, 1),
            28 => round($procentOnekg_total_procent, 1),
            29 => round($proteinOnekg_school_procent, 1),
            30 => round($proteinOnekg_home_procent, 1),
            31 => round($proteinOnekg_street_procent, 1),
            32 => '',
            34 => '',
            35 => '',
            33 => '',

            36 => $compliance_caloric_daily_energy,
            37 => $total_kkal,
            38 => $complianceCaloricDay,
            39 => round($complianceCaloricDay_procent, 1),
            40 => $normativ,
        );
    }
}
