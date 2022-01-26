<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "group".
 *
 * @property int $id ID
 * @property string $name
 * @property string $group_age
 * @property string $creat_at
 */
class Group extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'group';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [

            [['name'], 'required'],
            [['creat_at'], 'safe'],
            [['name', 'group_age'], 'string', 'max' => 150],
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
            'group_age' => 'Возрастная группа',
            'creat_at' => 'Creat At',
        ];
    }

    public function group_age($id)
    {
        if ($id == 1)
        {
            return '1-2';
        }
        elseif($id == 2)  {
            return '3-4';
        }
        elseif($id == 3)  {
            return '5-7';
        }
    }


        public function beforeSave($insert){
        if (parent::beforeSave($insert)) {
            $this->user_id = Yii::$app->user->id;
            return true;
        }
        return false;
    }
}
