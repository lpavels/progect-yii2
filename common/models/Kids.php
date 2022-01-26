<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "kids".
 *
 * @property int $id
 * @property int $user_id
 * @property int $organization_id
 * @property int $days_id описываемый день (кроме выходных)
 * @property int $height рост
 * @property int $mass масса
 * @property int $sex 0-ж, 1-м
 * @property int $class номер класса
 * @property int $use_telephone 0-нет, 1-да
 * @property int $charging делали ли зарядку? (0-нет, 1-да)
 * @property int $additional_education занимались ли в кружках доп обр? (0-нет,1-да)
 * @property int $sports_section занимались ли в спорт.секции? (0-нет,1-да)
 * @property int $sports_section1 первая спорт секция
 * @property int $sports_section2 вторая спорт секция
 * @property int $walk гуляли ли? (0-нет, 1-да)
 * @property int $sleep_day спали ли днем? (0-нет,1-да)
 * @property int $food_intake кол-во приемов пищи
 * @property string $created_at
 * @property string|null $updated_at
 */
class Kids extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'kids';
    }

    public function rules()
    {
        return [
            [['days_id', 'height', 'mass', 'age', 'sex', 'use_telephone', 'charging', 'additional_education', 'sports_section', 'walk', 'sleep_day', 'food_intake'], 'required'],
            [['user_id', 'organization_id', 'days_id', 'height', 'mass', 'age', 'sex', 'class', 'use_telephone', 'charging', 'additional_education', 'sports_section', 'walk', 'sleep_day', 'food_intake'], 'integer'],
            [['sports_section1','sports_section2','created_at', 'updated_at'], 'safe'],
            [['id','user_id'], 'unique'],

            ['sex', 'sex_validation'],
            [['height'], 'integer', 'min'=>37, 'max'=>250],
            [['mass'], 'integer', 'min'=>7, 'max'=>200],
            [['age'], 'integer', 'min'=>0, 'max'=>18],
            [['class'], 'class_validation'],
            [['days_id'], 'integer', 'min'=>1, 'max'=>5],
            [['charging','additional_education','sports_section','walk','sleep_day','use_telephone'], 'YesOrNo_validation'],
            [['food_intake'], 'integer', 'min'=>1, 'max'=>7],
        ];
    }

    public function sex_validation($attribute)
    {
        $ar = [0,1];
        $key = in_array($this->$attribute, $ar);
        if ($key == false)
        {
            $this->addError($attribute, 'Ошибка, выбрано несуществующее значение');
        }
    }

    public function class_validation($attribute)
    {
        $ar = [1,2,3,4,5,6,7,8,9,10,11];
        $key = in_array($this->$attribute, $ar);
        if ($key == false)
        {
            $this->addError($attribute, 'Ошибка, выбрано несуществующее значение');
        }
    }

    public function YesOrNo_validation($attribute)
    {
        $ar = [0,1];
        $key = in_array($this->$attribute, $ar);
        if ($key == false)
        {
            $this->addError($attribute, 'Ошибка, выбрано несуществующее значение');
        }
    }

    public function attributeLabels()
    {
        if (Yii::$app->user->can('kid') || Yii::$app->user->can('school1011') || Yii::$app->user->can('school14') || Yii::$app->user->can('school511') || Yii::$app->user->can('school56') || Yii::$app->user->can('school59') || Yii::$app->user->can('school79') || Yii::$app->user->can('student')){
            return [
                'id' => 'ID',
                'user_id' => 'User ID',
                'organization_id' => 'Organization ID',

                'height' => 'Длина тела (см)',
                'mass' => 'Масса тела (кг)',
                'sex' => 'Пол',
                'age' => 'Возраст (полных лет)',
                'class' => 'Класс',
                'days_id' => 'Описываемый день недели',
                'use_telephone' => 'Пользовались ли вы сотовым телефоном в этот день во время перемен?',
                'charging' => 'Делали ли вы зарядку в этот день?',
                'walk' => 'Гуляли ли вы на улице в этот день?',
                'additional_education' => 'Занимались ли вы в кружках(студиях) в этот день?',
                'sports_section' => 'Занимались ли вы в спортивной секции в этот день?',
                'sports_section1' => 'Вид спорта',
                'sports_section2' => 'Вид спорта (если занимались в двух)?',
                'sleep_day' => 'Спали ли вы днём в этот день?',
                'food_intake' => 'Сколько раз вы кушали в этот день?',
                'created_at' => 'Created At',
                'updated_at' => 'Updated At',
            ];
        }
        else
        {
            return [
                'id' => 'ID',
                'user_id' => 'User ID',
                'organization_id' => 'Organization ID',

                'height' => 'Длина тела (см)',
                'mass' => 'Масса тела (кг)',
                'sex' => 'Пол',
                'age' => 'Возраст (полных лет)',
                'class' => 'Класс',
                'days_id' => 'Описываемый день недели',
                'use_telephone' => 'Пользовался ли ребёнок сотовым телефоном во время перемен в школе (в течение дня в дет.саду)?',
                'charging' => 'Делал ли ребёнок зарядку в этот день?',
                'walk' => 'Гулял ли ребёнок на улице в этот день?',
                'additional_education' => 'Занимался ли ребёнок в кружках(студиях) в этот день?',
                'sports_section' => 'Занимался ли ребёнок в спортивной секции в этот день?',
                'sports_section1' => 'Вид спорта',
                'sports_section2' => 'Вид спорта (если занимался в двух)?',
                'sleep_day' => 'Спал ли ребёнок днём в этот день?',
                'food_intake' => 'Сколько раз ребёнок кушал в этот день?',
                'created_at' => 'Created At',
                'updated_at' => 'Updated At',
            ];
        }

    }
}
