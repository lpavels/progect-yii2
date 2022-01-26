<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "nutrition_procent".
 *
 * @property int $id
 * @property int $nutrition_id
 * @property float $procent
 * @property string $created_at
 */
class NutritionProcentActivity21 extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nutrition_procent';
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
            [['nutrition_id', 'procent'], 'required'],
            [['nutrition_id'], 'integer'],
            [['procent'], 'number'],
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
