<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "department_education".
 *
 * @property int $id
 * @property int $district_id
 * @property int $region_id
 * @property int $municipality_id
 * @property string $key_login_rpn
 * @property string $key_login_departament
 * @property string $created_at
 * @property string|null $updated_at
 */
class DepartmentEducation extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'department_education';
    }

    public function rules()
    {
        return [
            //[['district_id', 'region_id', 'municipality_id', 'key_login_rpn', 'key_login_departament'], 'required'],
            [['district_id', 'region_id', 'municipality_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['key_login_rpn','key_login_departament'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'district_id' => 'Федеральный округ',
            'region_id' => 'Регион',
            'municipality_id' => 'Муниципальное образование',
            'key_login_rpn' => 'Ключ Роспотребнадзора',
            'key_login_departament' => 'Ключ управления образования',
            'created_at' => 'Дата создания',
            'updated_at' => 'Updated At',
        ];
    }

    public function func_rand($f, $r, $m)
    {
        $qq = $f;
        $qq .= 'R';
        $qq .= $r;
        $qq .= 'MRMPM';
        $qq .= $m;

        do
        {
            $rand = mt_rand(1000, 9999);
            $qq .= $rand;
            $uniqueNumber = User::find()->select('key_login')->where(['key_login' => $qq])->one();
        } while ($uniqueNumber);
        return $qq;
    }
}
