<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "news_access".
 *
 * @property int $id
 * @property int $news_id
 * @property string $role
 * @property string $created_at
 */
class NewsAccess extends \yii\db\ActiveRecord
{
    public $guest;
    public $RPN;
    public $RPN_mun;
    public $director;
    public $kid;
    public $school14;
    public $school511;
    public $school56;
    public $school59;
    public $school79;
    public $school1011;
    public $student;
    public $parent;
    public $teacher;
    public $tutor;
    public $vospitatel;
    public $vospitatel_help;
    public $nyanya;
    public $medic;
    public $nutrition_specialist;
    public $otherwise;

    public static function tableName()
    {
        return 'news_access';
    }

    //public function rules()
    //{
    //    return [
    //        [['news_id', 'role'], 'required'],
    //        [['news_id'], 'integer'],
    //        [['created_at'], 'safe'],
    //        [['role'], 'string', 'max' => 50],
    //    ];
    //}

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'news_id' => 'New ID',
            'role' => 'Role',
            'created_at' => 'Created At',
        ];
    }
}
