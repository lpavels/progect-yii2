<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "training_themes".
 *
 * @property int $id
 * @property int $training_program_id Номер обучающей программы
 * @property int $theme_program_id Номер темы 
 * @property int|null $sort
 * @property string $creat_at
 */
class TrainingThemes extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'training_themes';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['training_program_id', 'theme_program_id'], 'required'],
            [['training_program_id', 'theme_program_id', 'sort'], 'integer'],
            [['creat_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'training_program_id' => 'Training Program ID',
            'theme_program_id' => 'Theme Program ID',
            'sort' => 'Sort',
            'creat_at' => 'Creat At',
        ];
    }
}
