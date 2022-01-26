<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "dishes_products".
 *
 * @property int $id
 * @property int|null $dishes_id
 * @property int|null $products_id
 * @property float|null $net_weight
 * @property float|null $gross_weight
 * @property string|null $created_at
 */
class DishesProductsActivity21 extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'dishes_products';
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
            [['dishes_id', 'products_id'], 'integer'],
            [['net_weight', 'gross_weight'], 'number'],
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
            'dishes_id' => 'Dishes ID',
            'products_id' => 'Products ID',
            'net_weight' => 'Net Weight',
            'gross_weight' => 'Gross Weight',
            'created_at' => 'Created At',
        ];
    }
}
