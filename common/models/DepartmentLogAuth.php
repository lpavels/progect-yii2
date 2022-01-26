<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "department_log_auth".
 *
 * @property int $id
 * @property int $user_id
 * @property string $auth_ip
 * @property string $created_at
 */
class DepartmentLogAuth extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'department_log_auth';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'auth_ip'], 'required'],
            [['user_id'], 'integer'],
            [['created_at'], 'safe'],
            [['auth_ip'], 'string', 'max' => 20],
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
            'auth_ip' => 'Auth Ip',
            'created_at' => 'Created At',
        ];
    }
}
