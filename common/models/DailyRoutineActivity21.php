<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "daily_routine".
 *
 * @property int $id
 * @property int $daily_routine_number_id
 * @property int|null $type_activitys_id
 * @property string|null $start_time
 * @property string|null $end_time
 * @property string $created_at
 */
class DailyRoutineActivity21 extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'daily_routine';
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
            [['daily_routine_number_id'], 'required'],
            [['daily_routine_number_id', 'type_activitys_id'], 'integer'],
            [['created_at'], 'safe'],
            [['start_time', 'end_time'], 'string', 'max' => 5],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'daily_routine_number_id' => 'Daily Routine Number ID',
            'type_activitys_id' => 'Type Activitys ID',
            'start_time' => 'Start Time',
            'end_time' => 'End Time',
            'created_at' => 'Created At',
        ];
    }
}
