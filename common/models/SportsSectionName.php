<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "sports_section_name".
 *
 * @property int $id
 * @property string $name
 * @property float $val вес 
 * @property string $created_at
 */
class SportsSectionName extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sports_section_name';
    }

    public function rules()
    {
        return [
            [['name', 'val'], 'required'],
            [['val'], 'number'],
            [['created_at'], 'safe'],
            [['name'], 'string', 'max' => 50],
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
            'val' => 'Val',
            'created_at' => 'Created At',
        ];
    }
}
