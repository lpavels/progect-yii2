<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "anket_parent_control".
 *
 * @property int $id
 * @property int $organization_id
 * @property int $date
 * @property string $name
 * @property int $menu_id
 * @property int $question1 Соответствует ли фактическое меню, объемы порций двухнедельному меню, утвержденному руководителем образовательной организации
 * @property int $question2 Организовано ли питание детей, требующих индивидуального подхода в организации питания с учетом имеющихся нарушений здоровья
 * @property int $question3 Все ли дети с сахарным диабетом и пищевой аллергией питаются вместе с другими детьми
 * @property int $question4 Все ли дети моют руки перед едой
 * @property int $question5 Созданы ли условия для мытья и дезинфекции рук
 * @property int $question6 Все ли дети едят сидя
 * @property int $question7 Все ли дети успевают поесть за перемену (хватает ли времени для приема пищи
 * @property int $question8 Есть ли замечания по чистоте посуды
 * @property int $question9 Есть ли замечания по чистоте столов
 * @property int $question10 Есть ли замечания к сервировке столов
 * @property int $question11 Теплые ли блюда выдаются детям
 * @property int $question12 Участвуют ли дети в накрывании на столы
 * @property int $question13 Лица, накрывающие на столы, работают в специальной одежде (халат, головной убор
 * @property int $question14 Организовано ли наряду с основным питанием дополнительное питание
 * @property int $count
 * @property int $masa_porcii
 * @property int $masa_othodov
 * @property string $created_at
 */
class AnketParentControl extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'anket_parent_control';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['organization_id', 'date', 'name', 'smena', 'peremena', 'question1', 'question2', 'question3', 'question4', 'question5', 'question6', 'question7', 'question8', 'question9', 'question10', 'question11', 'question12', 'question13', 'question14', 'count', 'masa_porcii', 'masa_othodov'], 'required'],
            [['organization_id', 'smena', 'peremena', 'question1', 'question2', 'question3', 'question4', 'question5', 'question6', 'question7', 'question8', 'question9', 'question10', 'question11', 'question12', 'question13', 'question14', 'count', 'masa_porcii', 'upolnomoch_org_id'], 'integer'],
            [['created_at'], 'safe'],
            [['masa_othodov'], 'double'],
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
            'organization_id' => 'Organization ID',
            'date' => 'Дата проведения мероприятия родительского контроля ',
            'name' => 'Ответственные лица (ФИО)',
            'smena' => 'Смена',
            'peremena' => 'Перемена',
            'question1' => '1.Соответствует ли фактическое меню, объемы порций двухнедельному меню, утвержденному руководителем образовательной организации?',
            'question2' => '2.Организовано ли питание детей, требующих индивидуального подхода в организации питания с учетом имеющихся нарушений здоровья (сахарный диабет, целиакия, пищевая аллергия)',
            'question3' => '3.Все ли дети с сахарным диабетом, пищевой аллергией, ОВЗ, фенилкетонурией, целиакией, муковисцидозом питаются в столовой? ',
            'question4' => '5.Все ли дети моют руки перед едой?',
            'question5' => '4.Созданы ли условия для мытья и дезинфекции рук? ',
            'question6' => '6.Все ли дети едят сидя? ',
            'question7' => '7.Все ли дети успевают поесть за перемену (хватает ли времени для приема пищи)?',
            'question8' => '8.Есть ли замечания по чистоте посуды?',
            'question9' => '9.Есть ли замечания по чистоте столов?',
            'question10' => '10.Есть ли замечания к сервировке столов?',
            'question11' => '11.Теплые ли блюда выдаются детям?',
            'question12' => '12.Участвуют ли дети в накрывании на столы?',
            'question13' => '13.Лица, накрывающие на столы, работают в специальной одежде (халат, головной убор)?',
            'question14' => '14.Организовано ли наряду с основным питанием дополнительное питание (возможность самостоятельного приобретения блюд через линию раздачи или буфет)? ',
            'count' => '15.Число детей, питающихся на данной перемене',
            'masa_porcii' => '16.Масса всех блюд на одного ребенка по меню(в граммах)',
            'masa_othodov' => '17.Общая масса несъеденной пищи (взвешивается несъеденная пища в КГ).',
            'created_at' => 'Дата сохранения',
        ];
    }



    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert))
        {
            if(Yii::$app->request->pathInfo == 'anket-parent-control/create')
            {
                $this->date = strtotime($this->date);
                $this->organization_id = Yii::$app->user->identity->organization_id;
                $this->status = 1;
                return true;
            }

            if(Yii::$app->request->pathInfo == 'anket-parent-control/inside')
            {
                $this->date = strtotime($this->date);
                $this->organization_id = Yii::$app->user->identity->organization_id;
                $this->status = 2;
                return true;
            }
            if(Yii::$app->request->pathInfo == 'anket-parent-control/social')
            {
                $this->date = strtotime($this->date);
                $this->upolnomoch_org_id = Yii::$app->user->identity->organization_id;
                $this->status = 3;
                return true;
            }
        }
        return false;
    }
    public function get_result($id)
    {
        $model = AnketParentControl::findOne($id);

        for($i=1;$i<=14;$i++){
            $question = 'question'.$i;
            if($i==8 || $i == 9 || $i == 10)
            {
                if ($model->$question == 1)
                {
                    $count = $count + 0;
                }
                if ($model->$question == 0)
                {
                    $count = $count + 2;
                }
            }else{
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


        $np = ($model->masa_othodov/$model->masa_porcii) * 100;
        if($np <= 20 ){
            $np_itog = 12;
        }
        elseif($np > 20 && $np <= 30){
            $np_itog = 8;
        }
        elseif($np > 40 && $np <= 50){
            $np_itog = 3;
        }
        elseif($np > 50 && $np <= 60){
            $np_itog = 1;
        }
        else{
            $np_itog = 0;
        }
        $itog = $count + $np_itog;
        return $itog;
    }

    public function get_result_test($id)
    {
        $model = AnketParentControl::findOne($id);
        for($i=1;$i<=14;$i++){
            $question = 'question'.$i;
            if($i == 8 || $i == 9 || $i == 10){
                if($model->$question == 1){
                    $count = $count + 0;
                }
                if($model->$question == 0){
                    $count = $count + 2;
                }
            }else{
                if($model->$question == 1){
                    $count = $count + 2;
                }
                if($model->$question == 2){
                    $count = $count + 1;
                }
                if($model->$question == 0){
                    $count = $count + 0;
                }
            }

        }

        return $count;
    }

    public function get_result_food($id, $field)
    {
        $model = AnketParentControl::findOne($id);
        if($field == 'procent'){
            if($model->masa_porcii == 0 || $model->count == 0){
                $np = 0;
            }
            else{
                $np = (($model->masa_othodov * 1000)/($model->masa_porcii * $model->count)) * 100;
            }
            return round($np, 1);
        }
        else{
            if($model->masa_porcii == 0 || $model->count == 0){
                $np = 0;
            }else{
                $np = (($model->masa_othodov * 1000)/($model->masa_porcii * $model->count)) * 100;
            }
            if($np <= 20 ){
                $np_itog = 12;
            }
            elseif($np > 20 && $np <= 30){
                $np_itog = 8;
            }
            elseif($np > 40 && $np <= 50){
                $np_itog = 3;
            }
            elseif($np > 50 && $np <= 60){
                $np_itog = 1;
            }
            else{
                $np_itog = 0;
            }
            return $np_itog;

        }
    }

    public function yes_no($quest, $answer, $field)
    {
        if($field == 'answer'){
            if($answer == 1){
                return 'Да';
            }
            if($answer == 0){
                return 'Нет';
            }
            if($answer == 3){
                return 'Частично';
            }
        }
        if($field == 'ball'){
            if($quest == 8 || $quest == 9 || $quest == 10){
                if($answer == 1){
                    return 0;
                }
                if($answer == 0){
                    return 2;
                }
            }else{
                if($answer == 1){
                    return 2;
                }
                if($answer == 3){
                    return 1;
                }
                if($answer == 0){
                    return 0;
                }
            }
        }
    }

}
