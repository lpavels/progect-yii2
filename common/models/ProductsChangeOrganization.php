<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "products_change_organization".
 *
 * @property int $id
 * @property int $organization_id
 * @property int $products_id
 * @property int $change_products_id
 * @property string $created_at
 */
class ProductsChangeOrganization extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'products_change_organization';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['organization_id', 'products_id', 'change_products_id'], 'required'],
            [['organization_id', 'products_id', 'change_products_id'], 'integer'],
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
            'organization_id' => 'Organization ID',
            'products_id' => 'Продукт',
            'change_products_id' => 'Продукт на который выполняется замена',
            'created_at' => 'Created At',
        ];
    }
}
