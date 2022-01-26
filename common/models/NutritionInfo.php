<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "nutrition_info".
 *
 * В ТАБЛИЦЕ ХРАНЯТСЯ ПРИЕМЫ ПИЩИ(ЗАВТРАК, ОБЕД И ТД)
 *
 * @property int $id
 * @property int $name
 * @property string $created_at
 */
class NutritionInfo extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nutrition_info';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string'],
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
            'name' => 'Название',
            'created_at' => 'Дата добавления в базу',
        ];
    }
}
