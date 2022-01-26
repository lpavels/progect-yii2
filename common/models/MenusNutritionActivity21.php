<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "menus_nutrition".
 *
 * @property int $id
 * @property int|null $menu_id
 * @property int|null $nutrition_id
 * @property string|null $created_at
 */
class MenusNutritionActivity21 extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'menus_nutrition';
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
            'created_at' => 'Created At',
        ];
    }
}
