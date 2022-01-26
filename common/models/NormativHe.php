<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "normativ_he".
 *
 * @property int $id
 * @property int $nutrition_id
 * @property int $age_info_id
 * @property int $sex
 * @property float $min_value
 * @property float $max_value
 * @property string $created_at
 */
class NormativHe extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'normativ_he';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nutrition_id', 'age_info_id', 'sex', 'min_value', 'max_value'], 'required'],
            [['nutrition_id', 'age_info_id', 'sex'], 'integer'],
            [['min_value', 'max_value'], 'number'],
            [['created_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nutrition_id' => 'Прием пищи',
            'age_info_id' => 'Возраст',
            'sex' => 'Пол',
            'min_value' => 'Минимальное значение норматива',
            'max_value' => 'Максимальное значение норматива',
            'created_at' => 'Дата создания норматива',
        ];
    }

    public function get_sex($id)
    {
        if($id == 1){
            return 'Мужской';
        }
        if($id == 2){
            return 'Женский';
        }
        return 'Оба';

    }

    public function get_nutrition($nutrition_id)
    {
        if($nutrition_id == 0){
            return 'Общее количество ХЕ за день';
        }
        $age = NutritionInfo::findOne($nutrition_id);
        return $age->name;
    }

    public function get_age($age_id)
    {
        $age = AgeInfo::findOne($age_id);
        return $age->name;
    }
}
