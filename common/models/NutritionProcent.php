<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "nutrition_procent".
 *
 * @property int $id
 * @property int $nutrition_id
 * @property int $procent
 * @property string $created_at
 */
class NutritionProcent extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nutrition_procent';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nutrition_id', 'procent'], 'required'],
            [['nutrition_id', 'procent'], 'integer'],
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
            'nutrition_id' => 'Nutrition ID',
            'procent' => 'Procent',
            'created_at' => 'Created At',
        ];
    }
}
