<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "age_info".
 *
 * В ТАБЛИЦЕ ХРАНИТСЯ СПИСОК ВОЗРАСТОВ ДЕТЕЙ
 *
 * @property int $id
 * @property string $name
 */
class AgeInfo extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'age_info';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
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
            'name' => 'Наименование',
        ];
    }
}
