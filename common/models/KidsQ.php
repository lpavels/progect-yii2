<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "kids_q".
 *
 * @property int $id ID
 * @property int $name_organization_id
 * @property int $user_id Пользователь
 * @property int $group_id Пользователь
 * @property int $topic Какую тему прошел!
 * @property string $lastname
 * @property string $name
 * @property string $creat_at
 */
class KidsQ extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'kids_q';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['group_id', 'sex','lastname', 'name'], 'required'],
            [['group_id', 'sex'], 'integer'],
            [['lastname', 'name'], 'string', 'max' => 150],
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
            'group_id' => 'Группа',
            'sex' => 'Пол',
            'lastname' => 'Фамилия',
            'name' => 'Имя',
            'creat_at' => 'Creat At',
        ];
    }
    public function sex($id)
    {
        $sex_item = ['мужской','женский'];
        if ($id == 1)
        {
            return 'мужской';
        }
        else  {
            return 'женский';
        }
    }
    public function group($id)
    {
        $questions_tupe3 = Group::find()->where(['id' => $id])->one();
        $q = $questions_tupe3->name;

        return $q;
    }
}
