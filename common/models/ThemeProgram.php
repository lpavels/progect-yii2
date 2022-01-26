<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "theme_program".
 *
 * @property int $id ID
 * @property string $name название
 * @property string|null $short_name
 * @property int|null $sort
 * @property string $creat_at
 */
class ThemeProgram extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'theme_program';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['creat_at'], 'safe'],
            [['name'], 'string', 'max' => 400],
            [['short_name'], 'string', 'max' => 255],
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
            'short_name' => 'Short Name',
            'creat_at' => 'Creat At',
        ];
    }
}
