<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "dishes_category".
 *
 * В ТАБЛИЦЕ ХРАНЯТСЯ КАТЕГОРИИ БЛЮД
 *
 * @property int $id
 * @property string $name
 * @property string $created_at
 */
class DishesCategory extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'dishes_category';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['created_at'], 'safe'],
            [['sort'], 'integer'],
            [['name'], 'string', 'max' => 255],
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
            'sort' => 'Приоритет в меню',
            'created_at' => 'Дата добавления в базу',
        ];
    }
}
