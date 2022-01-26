<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "menus_nutrition".
 *
 * @property int $id
 * @property int $menu_id
 * @property int|null $nutrition_id
 * @property string $created_at
 */
class MenusNutrition extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'menus_nutrition';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['menu_id'], 'required'],
            [['menu_id', 'nutrition_id'], 'integer'],
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
            'menu_id' => 'Menu ID',
            'nutrition_id' => 'Nutrition ID',
            'created_at' => 'Дата добавления в базу',
        ];
    }
}
