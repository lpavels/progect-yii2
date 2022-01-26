<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "products_change".
 *
 * @property int $id
 * @property int $products_id
 * @property int $change_products_id
 * @property string $created_at
 */
class ProductsChange extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'products_change';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['products_id', 'change_products_id'], 'required'],
            [['products_id', 'change_products_id'], 'integer'],
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
            'products_id' => 'Продукт',
            'change_products_id' => 'Продукт на который можно заменить',
            'created_at' => 'Created At',
        ];
    }
}
