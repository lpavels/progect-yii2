<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "change_personal_data".
 *
 * @property int $id
 * @property int $user_id
 * @property string $name_change
 * @property string $changed_to
 * @property string $change_user
 * @property string $created_at
 */
class ChangePersonalData extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'change_personal_data';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'name_change'], 'required'],
            [['user_id'], 'integer'],
            [['created_at'], 'safe'],
            [['name_change'], 'string', 'max' => 250],
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
            'name_change' => 'Name Change',
            'created_at' => 'Created At',
        ];
    }
}
