<?php

namespace common\models;

use Yii;
use yii\base\Model;

class DailyRoutineFormActivity21 extends Model
{
    public $dailyRoutineNumberId;

    public $typeActivitysID1;
    public $typeActivitysID2;
    public $typeActivitysID3;
    public $typeActivitysID4;
    public $typeActivitysID5;
    public $typeActivitysID6;
    public $typeActivitysID7;
    public $typeActivitysID8;
    public $typeActivitysID9;
    public $typeActivitysID10;
    public $typeActivitysID11;
    public $typeActivitysID12;
    public $typeActivitysID13;
    public $typeActivitysID14;
    public $typeActivitysID15;
    public $typeActivitysID16;
    public $typeActivitysID17;
    public $typeActivitysID18;
    public $typeActivitysID19;
    public $typeActivitysID20;
    public $typeActivitysID21;
    public $typeActivitysID22;
    public $typeActivitysID23;
    public $typeActivitysID24;
    public $typeActivitysID25;
    public $typeActivitysID26;
    public $typeActivitysID27;
    public $typeActivitysID28;
    public $typeActivitysID29;
    public $typeActivitysID30;

    public $startTime1;
    public $startTime2;
    public $startTime3;
    public $startTime4;
    public $startTime5;
    public $startTime6;
    public $startTime7;
    public $startTime8;
    public $startTime9;
    public $startTime10;
    public $startTime11;
    public $startTime12;
    public $startTime13;
    public $startTime14;
    public $startTime15;
    public $startTime16;
    public $startTime17;
    public $startTime18;
    public $startTime19;
    public $startTime20;
    public $startTime21;
    public $startTime22;
    public $startTime23;
    public $startTime24;
    public $startTime25;
    public $startTime26;
    public $startTime27;
    public $startTime28;
    public $startTime29;
    public $startTime30;

    public $endTime1;
    public $endTime2;
    public $endTime3;
    public $endTime4;
    public $endTime5;
    public $endTime6;
    public $endTime7;
    public $endTime8;
    public $endTime9;
    public $endTime10;
    public $endTime11;
    public $endTime12;
    public $endTime13;
    public $endTime14;
    public $endTime15;
    public $endTime16;
    public $endTime17;
    public $endTime18;
    public $endTime19;
    public $endTime20;
    public $endTime21;
    public $endTime22;
    public $endTime23;
    public $endTime24;
    public $endTime25;
    public $endTime26;
    public $endTime27;
    public $endTime28;
    public $endTime29;
    public $endTime30;

    public function rules()
    {
        return [
            /*table kids*/
            ['startTime1', 'ip'],
            //[['user_id', 'last_name', 'name', 'federal_district_id', 'region_id', 'municipality_id', 'type_municipality', 'type_kids_id','name_organization', 'educational_institution_select',  'sex', 'birth', 'height', 'mass'], 'required'],
            //[['user_id', 'federal_district_id', 'region_id', 'municipality_id', 'type_municipality', 'type_kids_id', 'sex', 'birth', 'height', 'mass', 'type_organiz_id'], 'integer'],
            //[['type_organiz_id','created_at'], 'safe'],
            //[['last_name', 'name'], 'string', 'max' => 50],
            //[['name_organization'], 'string', 'max' => 250],
            /*table kids_deseases*/
            //[['body_length', 'body_weight', 'capacity_lungs', 'left_hand', 'right_hand', 'bmi', 'physical_evolution', 'health_group', 'physical_group_id', 'flat_feet', 'date'], 'required'],
            //[['respiratory','digestive_system','circulations','diabetes','celiac','food_allergy','citrus','nuts','egg','milk','chocolate','fish','other_allergy1'], 'string', 'max' => 5],
            //[['other_allergy2'], 'string', 'max' => 250],
            //[['created_at'], 'safe'],
        ];
    }

    public function get_total($id, $status)
    {
        $recommended_value_boys = [
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
        ];
        $recommended_value_girls = [
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
            17 => [45.213, 24.4, 3.7, 17.1]
        ];

        $modelDailyRoutineNumber = DailyRoutineNumberActivity21::findOne($id);
        $kids_model = KidsActivity21::findOne($modelDailyRoutineNumber['kids_id']); //ребенок
		
        $modelActivitys = DailyRoutineActivity21::find()->where(['daily_routine_number_id' => $id])->all(); //виды деятельности
		if(empty($kids_model)){
		    return 0;
		}
        $bodyMassIndex = $kids_model->get_imt2($kids_model['height'], $kids_model['mass'], $kids_model['type_kids_id'], $kids_model['sex'], $kids_model['birth'], 1);
        $minRecBodyMass = $kids_model->get_imt2($kids_model['height'], $kids_model['mass'], $kids_model['type_kids_id'], $kids_model['sex'], $kids_model['birth'], 2);
        //$maxRecBodyMass = $kids_model->get_imt2($kids_model['height'], $kids_model['mass'], $kids_model['type_kids_id'], $kids_model['sex'], $kids_model['birth'], 3);
			
        /* сбор в массив видов деятельности */
        $typeActivitysId = [];
        $startTime = [];
        $endTime = [];
        $time = [];
        foreach ($modelActivitys as $modelActivity)
        {
            $typeActivitysId[] .= $modelActivity['type_activitys_id'];
            $startTime[] .= $modelActivity['start_time'];
            $endTime[] .= $modelActivity['end_time'];
            $time[] .= $this->calculationTimeFunc($modelActivity['start_time'], $modelActivity['end_time'], 1);
        }
        $timeAll = array_sum($time);//общее время
        if ($kids_model['sex'] == 0)
        {
            if ($kids_model['birth'] >17) $calculationRecValue = $recommended_value_boys[17];
            else $calculationRecValue = $recommended_value_boys[$kids_model['birth']];
        }
        if ($kids_model['sex'] == 1)
        {
            if ($kids_model['birth'] >17) $calculationRecValue = $recommended_value_girls[17];
            else $calculationRecValue = $recommended_value_girls[$kids_model['birth']];
        }

        if ($bodyMassIndex === 'Дефицит массы тела')
        {
            $totalEnergy_rec = $minRecBodyMass * $calculationRecValue[0];
            $OO_fact = $totalEnergy_rec * 0.54;
            $SDDP_fact = $totalEnergy_rec * 0.081;
            $OO_rec = $totalEnergy_rec * 0.54;
            $SDDP_rec = $totalEnergy_rec * 0.081;
            $DA_rec = $totalEnergy_rec - $OO_rec - $SDDP_rec;
        }
        else {
            if ($bodyMassIndex === 'Нормальный вес')
            {
                $totalEnergy_rec = $kids_model['mass'] * $calculationRecValue[0];
                $OO_fact = $totalEnergy_rec * 0.54;
                $SDDP_fact = $totalEnergy_rec * 0.081;
                $OO_rec = $totalEnergy_rec * 0.54;
                $SDDP_rec = $totalEnergy_rec * 0.081;
                $DA_rec = $totalEnergy_rec - $OO_rec - $SDDP_rec;
            }
            else
            {
                if ($bodyMassIndex === 'Избыточная масса тела')
                {
                    $totalEnergy_rec = $minRecBodyMass * $calculationRecValue[0];
                    $OO_fact = $totalEnergy_rec * 0.54;
                    $SDDP_fact = $totalEnergy_rec * 0.081;
                    $OO_rec = $totalEnergy_rec * 0.54;
                    $SDDP_rec = $totalEnergy_rec * 0.081;
                    $DA_rec = $totalEnergy_rec - $OO_rec - $SDDP_rec;
                }
                else
                {
                    if ($bodyMassIndex === 'Ожирение')
                    {
                        $totalEnergy_rec = $minRecBodyMass * $calculationRecValue[0];
                        $OO_fact = $totalEnergy_rec * 0.54;
                        $SDDP_fact = $totalEnergy_rec * 0.081;
                        $OO_rec = $totalEnergy_rec * 0.54;
                        $SDDP_rec = $totalEnergy_rec * 0.081;
                        $DA_rec = $totalEnergy_rec - $OO_rec - $SDDP_rec;
                    }
                }
            }
        }

        if ($totalEnergy_rec==0)
        {
            print_r('При выполнении самостоятельной работы вы не указали возраст. Перейдите по ссылке <a href="http://individ.demography.site/kids">individ.demography.site</a> и внесите возраст ребёнка. (Номер ребёнка:'.$modelDailyRoutineNumber['kids_id'].'. Идентификационный номер: '.UsersActivity21::findOne(['id'=>$kids_model->user_id])->key_login.')');
            die();
        }

        $unoccupied = (1440 - $timeAll) / 3; //незаполненое время
        $otherEnergy = (!$unoccupied) ? 0 : (($unoccupied * 0.01) + ($unoccupied * 0.015) + ($unoccupied * 0.0397));

        $DA_fact = 0;
        for ($g = 0; $g < count($typeActivitysId); $g++)
        {
            $typeActivitysNorm = TypeActivitysActivity21::findOne($typeActivitysId[$g]);
            $energy_temp = $typeActivitysNorm['norm'];
            $DA_fact = $DA_fact + ($energy_temp * $time[$g] * $kids_model['mass']);
        }/*расчёт ДА*/
        $DA_fact = $DA_fact + $otherEnergy;
        $totalEnergy_fact = ($OO_fact + $SDDP_fact + $DA_fact);
        $OOp_fact = $OO_fact / $totalEnergy_fact * 100;
        $SDDPp_fact = $SDDP_fact / $totalEnergy_fact * 100;
        $DAp_fact = $DA_fact / $totalEnergy_fact * 100;

        $OOp_rec = $OO_rec / $totalEnergy_rec * 100;
        $SDDPp_rec = $SDDP_rec / $totalEnergy_rec * 100;
        $DAp_rec = ($DA_rec + $otherEnergy) / $totalEnergy_rec * 100;

        if ($status == 1)
        {
            return round($OO_fact, 1) . ' (' . round($OOp_fact, 1) . '%)';
        }
        if ($status == 2)
        {
            return round($SDDP_fact, 1) . ' (' . round($SDDPp_fact, 1) . '%)';
        }
        if ($status == 3)
        {
            return round($DA_fact, 1) . ' (' . round($DAp_fact, 1) . '%)';
        }
        if ($status == 4)
        {
            return round($totalEnergy_fact, 1) . ' (100%)';
        }
        if ($status == 5)
        {
            return round($OO_rec, 1) . ' (' . round($OOp_rec, 1) . '%)';
        }
        if ($status == 6)
        {
            return round($SDDP_rec, 1) . ' (' . round($SDDPp_rec, 1) . '%)';
        }
        if ($status == 7)
        {
            return round($DA_rec, 1) . ' (' . round($DAp_rec, 1) . '%)';
        }
        if ($status == 8)
        {
            return round($totalEnergy_rec, 1) . ' (100%)';
        }

        if ($status == 'check')
        {
            return round($totalEnergy_fact, 1);
        }
    }

    public function calculationTimeFunc($startTimeNum, $endTimeNum, $status)
    {
        if ($status == 1)
        {
            $dif_time = floor((strtotime($endTimeNum) - strtotime($startTimeNum)) / 60);
            if ($dif_time <0)
            {
                return 1440 + $dif_time;
            }
            else return $dif_time;
        }
    }
}