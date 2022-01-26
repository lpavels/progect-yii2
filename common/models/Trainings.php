<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "trainings".
 *
 * @property int $id ID
 * @property int $user_id Пользователь
 * @property int $topic Какую тему прошел!
 * @property string $key Ключ из двиг атктивности!
 * @property string $creat_at
 */
class Trainings extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trainings';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'theme_program_id'], 'required'],
            [['user_id', 'theme_program_id'], 'integer'],
            [['creat_at'], 'safe'],
            [['key'], 'string', 'max' => 150],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'topic' => 'Topic',
            'key' => 'Key',
            'creat_at' => 'Creat At',
        ];
    }
}
