<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "normativ_prognos_storage".
 *
 * @property int $id
 * @property int $type_organization_id
 * @property int $age_info_id
 * @property int $products_category_id
 * @property float $value
 */
class NormativPrognosStorage extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'normativ_prognos_storage';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type_organization_id', 'age_info_id', 'products_category_id', 'value'], 'required'],
            [['type_organization_id', 'age_info_id', 'products_category_id'], 'integer'],
            [['value'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type_organization_id' => 'Тип организации',
            'age_info_id' => 'Возраст',
            'products_category_id' => 'Группа продуктов',
            'value' => 'Значение норматива',
        ];
    }

    public function get_products_category($id){
        $category = ProductsCategory::findOne($id);
        return $category->name;
    }

    public function get_age($id){
        $category = AgeInfo::findOne($id);
        return $category->name;
    }

    public function get_org($id){
        $category = TypeOrganization::findOne($id);
        return $category->name;
    }
}
