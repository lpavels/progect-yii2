<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "daily_routine_number".
 *
 * @property int $id
 * @property int $kids_id
 * @property string $created_at
 * @property string|null $updated_at
 */
class DailyRoutineNumberActivity21 extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'daily_routine_number';
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
            [['kids_id'], 'required'],
            [['kids_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'kids_id' => 'Kids ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
