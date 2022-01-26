<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "users".
 *
 * @property int $id
 * @property string $key_login
 * @property int $federal_district_id
 * @property int $region_id
 * @property int $municipality_id
 * @property int $type_municipality
 * @property int $users_program
 * @property int $status
 * @property int $created_at
 * @property int|null $updated_at
 * @property string|null $auth_key
 */
class UsersActivity21 extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'users';
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
            [['key_login', 'federal_district_id', 'region_id', 'municipality_id','fio', 'type_municipality', 'users_program', 'status'], 'required'],
            [['federal_district_id', 'region_id', 'municipality_id', 'type_municipality', 'users_program', 'status'], 'integer'],
            [['key_login'], 'string', 'max' => 250],
            [['auth_key'], 'string', 'max' => 32],
            [[ 'created_at', 'updated_at'], 'safe']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'key_login' => 'Key Login',
            'federal_district_id' => 'Federal District ID',
            'region_id' => 'Region ID',
            'municipality_id' => 'Municipality ID',
            'type_municipality' => 'Type Municipality',
            'users_program' => 'Users Program',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'auth_key' => 'Auth Key',
        ];
    }
}
