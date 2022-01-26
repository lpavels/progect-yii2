<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "menus_days".
 *
 * @property int $id
 * @property int $days_id
 * @property string $created_at
 */
class MenusDays extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'menus_days';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['days_id', 'menu_id'], 'required'],
            [['days_id', 'menu_id'], 'integer'],
            [['created_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'menu_id' => 'menu ID',
            'days_id' => 'Days ID',
            'created_at' => 'Дата добавления в базу',
        ];
    }
}
