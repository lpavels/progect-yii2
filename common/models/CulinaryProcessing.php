<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "culinary_processing".
 *
 * В ТАБЛИЦЕ ХРАНЯТСЯ ПРОЦЕССЫ(ВИДЫ ИЗГОТОВЛЕНИЯ: "ВАРКА, ЗАПЕКАНИЕ И ТД")
 *
 * @property int $id
 * @property string $name
 * @property string $created_at
 */
class CulinaryProcessing extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'culinary_processing';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['created_at'], 'safe'],
            [['name'], 'string', 'max' => 255],
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
            'created_at' => 'Дата создания',
        ];
    }
}
