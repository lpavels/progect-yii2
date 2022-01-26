<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "type_activitys".
 *
 * @property int $id
 * @property string $name
 * @property string $type
 * @property float $norm
 * @property string $created_at
 */
class TypeActivitysActivity21 extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'type_activitys';
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
            [['name', 'type'], 'required'],
            [['norm'], 'number'],
            [['created_at'], 'safe'],
            [['name'], 'string', 'max' => 250],
            [['type'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'type' => 'Type',
            'norm' => 'Norm',
            'created_at' => 'Created At',
        ];
    }
}
