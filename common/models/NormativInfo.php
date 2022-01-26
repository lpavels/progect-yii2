<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "normativ_info".
 *
 * @property int $id
 * @property int $age_info_id
 * @property int $nutrition_info_id
 * @property float $kkal
 * @property float $kkal_min_procent
 * @property float $kkal_middle_procent
 * @property float $kkal_max_procent
 * @property float $min_kkal
 * @property float $middle_kkal
 * @property float $max_kkal
 * @property float $protein_min_kkal
 * @property float $protein_middle_kkal
 * @property float $protein_max_kkal
 * @property float $fat_min_kkal
 * @property float $fat_middle_kkal
 * @property float $fat_max_kkal
 * @property float $carbohydrates_min_kkal
 * @property float $carbohydrates_middle_kkal
 * @property float $carbohydrates_max_kkal
 * @property float $protein_min_procent
 * @property float $protein_middle_procent
 * @property float $protein_max_procent
 * @property float $fat_min_procent
 * @property float $fat_middle_procent
 * @property float $fat_max_procent
 * @property float $carbohydrates_min_procent
 * @property float $carbohydrates_middle_procent
 * @property float $carbohydrates_max_procent
 * @property float $itogo_min
 * @property float $itogo_middle
 * @property float $itogo_max
 * @property float $protein_min_weight
 * @property float $protein_middle_weight
 * @property float $protein_max_weight
 * @property float $fat_min_weight
 * @property float $fat_middle_weight
 * @property float $fat_max_weight
 * @property float $carbohydrates_min_weight
 * @property float $carbohydrates_middle_weight
 * @property float $carbohydrates_max_weight
 * @property string $created_at
 */
class NormativInfo extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'normativ_info';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['age_info_id', 'nutrition_info_id', 'kkal', 'kkal_min_procent', 'kkal_middle_procent', 'kkal_max_procent', 'min_kkal', 'middle_kkal', 'max_kkal', 'protein_min_kkal', 'protein_middle_kkal', 'protein_max_kkal', 'fat_min_kkal', 'fat_middle_kkal', 'fat_max_kkal', 'carbohydrates_min_kkal', 'carbohydrates_middle_kkal', 'carbohydrates_max_kkal', 'protein_min_procent', 'protein_middle_procent', 'protein_max_procent', 'fat_min_procent', 'fat_middle_procent', 'fat_max_procent', 'carbohydrates_min_procent', 'carbohydrates_middle_procent', 'carbohydrates_max_procent', 'itogo_min', 'itogo_middle', 'itogo_max', 'protein_min_weight', 'protein_middle_weight', 'protein_max_weight', 'fat_min_weight', 'fat_middle_weight', 'fat_max_weight', 'carbohydrates_min_weight', 'carbohydrates_middle_weight', 'carbohydrates_max_weight'], 'required'],
            [['age_info_id', 'nutrition_info_id'], 'integer'],
            [['kkal', 'kkal_min_procent', 'kkal_middle_procent', 'kkal_max_procent', 'min_kkal', 'middle_kkal', 'max_kkal', 'protein_min_kkal', 'protein_middle_kkal', 'protein_max_kkal', 'fat_min_kkal', 'fat_middle_kkal', 'fat_max_kkal', 'carbohydrates_min_kkal', 'carbohydrates_middle_kkal', 'carbohydrates_max_kkal', 'protein_min_procent', 'protein_middle_procent', 'protein_max_procent', 'fat_min_procent', 'fat_middle_procent', 'fat_max_procent', 'carbohydrates_min_procent', 'carbohydrates_middle_procent', 'carbohydrates_max_procent', 'itogo_min', 'itogo_middle', 'itogo_max', 'protein_min_weight', 'protein_middle_weight', 'protein_max_weight', 'fat_min_weight', 'fat_middle_weight', 'fat_max_weight', 'carbohydrates_min_weight', 'carbohydrates_middle_weight', 'carbohydrates_max_weight'], 'number'],
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
            'age_info_id' => 'Возрастная категория',
            'nutrition_info_id' => 'Прием пищи',
            'kkal' => 'Энергетическая ценность(за все приемы пищи), калории',
            'kkal_min_procent' => 'Минимальный процент калорий',
            'kkal_middle_procent' => 'Средний процент калорий',
            'kkal_max_procent' => 'Максимальный процент калорий',
            'min_kkal' => 'Минимальная суммарная калорийность(ккал)',
            'middle_kkal' => 'Средняя суммарная калорийность(ккал)',
            'max_kkal' => 'Максимальная суммарная калорийность(ккал)',
            'protein_min_kkal' => 'Белки минимальная эн.ценность(ккал)',
            'protein_middle_kkal' => 'Белки средняя эн.ценность(ккал)',
            'protein_max_kkal' => 'Белки максимальная эн.ценность(ккал)',
            'fat_min_kkal' => 'Жиры минимальная эн.ценность(ккал)',
            'fat_middle_kkal' => 'Жиры средняя эн.ценность(ккал)',
            'fat_max_kkal' => 'Жиры максимальная эн.ценность(ккал)',
            'carbohydrates_min_kkal' => 'Углеводы минимальная эн.ценность(ккал)',
            'carbohydrates_middle_kkal' => 'Углеводы средняя эн.ценность(ккал)',
            'carbohydrates_max_kkal' => 'Углеводы максимальная эн.ценность(ккал)',
            'protein_min_procent' => 'Белки минимальная эн.ценность(в % от общей каллорийности за прием пищи)',
            'protein_middle_procent' => 'Белки средняя эн.ценность(в % от общей каллорийности за прием пищи)',
            'protein_max_procent' => 'Белки максимальная эн.ценность(в % от общей каллорийности за прием пищи)',
            'fat_min_procent' => 'Жиры минимальная эн.ценность(в % от общей каллорийности за прием пищи)',
            'fat_middle_procent' => 'Жиры средняя эн.ценность(в % от общей каллорийности за прием пищи)',
            'fat_max_procent' => 'Жиры максимальная эн.ценность(в % от общей каллорийности за прием пищи)',
            'carbohydrates_min_procent' => 'Углеводы минимальная эн.ценность(в % от общей каллорийности за прием пищи)',
            'carbohydrates_middle_procent' => 'Углеводы средняя эн.ценность(в % от общей каллорийности за прием пищи)',
            'carbohydrates_max_procent' => 'Углеводы максимальная эн.ценность(в % от общей каллорийности за прием пищи)',
            'itogo_min' => 'ИТОГО минимальная эн.ценность(в % от общей каллорийности за прием пищи)',
            'itogo_middle' => 'ИТОГО средняя эн.ценность(в % от общей каллорийности за прием пищи)',
            'itogo_max' => 'ИТОГО максимальная эн.ценность(в % от общей каллорийности за прием пищи)',
            'protein_min_weight' => 'Белки минимальная масса(гр)',
            'protein_middle_weight' => 'Белки средняя масса(гр)',
            'protein_max_weight' => 'Белки масса масса(гр)',
            'fat_min_weight' => 'Жиры минимальная масса(гр)',
            'fat_middle_weight' => 'Жиры средняя масса(гр)',
            'fat_max_weight' => 'Жиры максимальная масса(гр)',
            'carbohydrates_min_weight' => 'Углеводы минимальная масса(гр)',
            'carbohydrates_middle_weight' => 'Углеводы средняя масса(гр)',
            'carbohydrates_max_weight' => 'Углеводы максимальная масса(гр)',
            //'status' => 'Статус',
            'created_at' => 'Дата добавления норматива',
        ];
    }

    public function get_age($age_id)
    {
        $age = AgeInfo::findOne($age_id);
        return $age->name;
    }

    public function get_nutrition($nutrition_id)
    {
        if($nutrition_id == 0){
            return 'ИТОГ за все приемы пищи по данной возрастной категории';
        }
        $age = NutritionInfo::findOne($nutrition_id);
        return $age->name;
    }
}
