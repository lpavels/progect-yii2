<?php

namespace common\models;

use Yii;
use common\models\FeedersCharacters;
use common\models\AgeInfo;



/**
 * This is the model class for table "menus".
 *
 * @property int $id
 * @property int $organization_id
 * @property int $feeders_characters_id
 * @property int $age_info_id
 * @property string $name
 * @property int $cycle
 * @property string|null $date_start
 * @property string|null $date_end
 * @property int $status_archive
 * @property string $created_at
 */
class Menus extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'menus';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['organization_id', 'feeders_characters_id', 'age_info_id', 'name', 'cycle'], 'required'],
            [['organization_id', 'feeders_characters_id', 'age_info_id', 'cycle', 'status_archive', 'show_indicator', 'parent_id'], 'integer'],
            [['date_start', 'date_end', 'created_at', 'show_indicator'], 'safe'],
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
            'organization_id' => 'Организация',
            'feeders_characters_id' => 'Характеристика питающихся',
            'parent_id' => 'parent_id',
            'age_info_id' => 'Возрастная категория',
            'name' => 'Название',
            'cycle' => 'Количество недель (цикл)',
            'date_start' => 'Дата начала',
            'date_end' => 'Дата окончания',
            'status_archive' => 'Статус архива',
            'show_indicator' => 'Настройка видимости',
            'created_at' => 'Дата создания меню',
        ];
    }

    public function get_characters($id){
        $category = FeedersCharacters::findOne($id);
        return $category->name;
    }

    public function get_date($id){

        $menus = strtotime(Menus::findOne($id)->created_at);
        $date = date( "d.m.Y", $menus);

        return $date;
    }

    public function get_age($id){
        $category = AgeInfo::findOne($id);
        return $category->name;
    }

    public function get_days($menu_id, $field){

            $menus_days = MenusDays::find()->where(['menu_id' => $menu_id])->all();
            $days = '';
            foreach($menus_days as $m_day){
                $days.= Days::findOne($m_day->days_id)->$field. ' ';
            }
            return $days;

        //return 'not ok';
    }

    public function get_nutritions($menu_id){

        $menus_days = MenusNutrition::find()->where(['menu_id' => $menu_id])->all();
        $days = '';
        foreach($menus_days as $m_day){
            $days.= NutritionInfo::findOne($m_day->nutrition_id)->name. ' ';
        }
        return $days;

        //return 'not ok';
    }

    public function get_count_download($id){
        $count = Menus::find()->where(['parent_id' => $id])->count();
        return $count;
    }

    public function get_organization($id){
        $name = Organization::findOne($id);
        /*if($name->id == Yii::$app->user->identity->organization_id){
            return '<b style="color:red">'.$name->title.'</b>';
        }*/
        return $name->title;
    }
	
	public function get_monitoring_common($id, $field){
        if($field == 'municipality'){
            $organizations = Organization::find()->where(['municipality_id' => $id, 'type_org' => 3])->all();
        }
        if($field == 'organization'){
            $organizations = Organization::find()->where(['id' => $id, 'type_org' => 3])->all();
        }

        $result = [];
        $count_zavtrak = 0;
        $count_obed = 0;
        $count_vse = 0;
        foreach ($organizations as $organization){
            $information_education = InformationEducation::find()->where(['organization_id' => $organization->id])->one();
            $rashodi = ExpensesFood::find()->where(['organization_id' => $organization->id])->one();
            $result['quantity14'] = $result['quantity14'] + $information_education->quantity14;
            $result['quantity14_first'] = $result['quantity14_first'] + $information_education->quantity14_first;
            $result['quarter1'] = $result['quarter1'] + $rashodi->quarter1;
            $result['quarter2'] = $result['quarter2'] + $rashodi->quarter2;
            $result['quarter3'] = $result['quarter3'] + $rashodi->quarter3;
            $result['quarter4'] = $result['quarter4'] + $rashodi->quarter4;
            $result['federal'] = $result['federal'] + $rashodi->federal;
            $result['region'] = $result['region'] + $rashodi->region;
            $result['municipality'] = $result['municipality'] + $rashodi->municipality;

            $result['expenses_zavtrak'] = $result['expenses_zavtrak'] + $rashodi->expenses_zavtrak;
            $result['expenses_obed'] = $result['expenses_obed'] + $rashodi->expenses_obed;
            $result['expenses_zavtrak_obed'] = $result['expenses_zavtrak_obed'] + $rashodi->expenses_zavtrak_obed;

            if($rashodi->expenses_zavtrak > 0){
                $count_zavtrak++;
            }
            if($rashodi->expenses_obed > 0){
                $count_obed++;
            }
            if($rashodi->expenses_zavtrak_obed > 0){
                $count_vse++;
            }

        }
        if($count_zavtrak == 0){
            $result['expenses_zavtrak'] = 'н/д';
        }else{
            $result['expenses_zavtrak'] = $result['expenses_zavtrak']/$count_zavtrak;
        }

        if($count_obed == 0){
            $result['expenses_obed'] = 'н/д';
        }else{
            $result['expenses_obed'] = $result['expenses_obed']/$count_obed;
        }

        if($count_vse == 0){
            $result['expenses_zavtrak_obed'] = 'н/д';
        }else{
            $result['expenses_zavtrak_obed'] = $result['expenses_zavtrak_obed']/$count_vse;
        }
        
        return $result;
    }
}
