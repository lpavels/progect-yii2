<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "products".
 *
 * @property int $id
 * @property string|null $name
 * @property int|null $products_category_id
 * @property int|null $products_subcategory_id
 * @property int|null $sort
 * @property float|null $water
 * @property float|null $protein
 * @property float|null $fat
 * @property float|null $carbohydrates_total
 * @property float|null $carbohydrates_saccharide
 * @property float|null $carbohydrates_starch
 * @property float|null $carbohydrates_lactose
 * @property float|null $carbohydrates_sacchorose
 * @property float|null $carbohydrates_cellulose
 * @property float|null $dust_total
 * @property float|null $dust_nacl
 * @property float|null $apple_acid
 * @property float|null $na
 * @property float|null $k
 * @property float|null $ca
 * @property float|null $mg
 * @property float|null $p
 * @property float|null $fe
 * @property float|null $i
 * @property float|null $se
 * @property float|null $f
 * @property float|null $vitamin_a
 * @property float|null $vitamin_b_carotene
 * @property float|null $vitamin_b1
 * @property float|null $vitamin_b2
 * @property float|null $vitamin_pp
 * @property float|null $vitamin_c
 * @property float|null $vitamin_d
 * @property float|null $energy_kkal
 * @property float|null $energy_kdj
 * @property string|null $created_at
 */
class ProductsActivity21 extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'products';
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
            [['products_category_id', 'products_subcategory_id', 'sort'], 'integer'],
            [['water', 'protein', 'fat', 'carbohydrates_total', 'carbohydrates_saccharide', 'carbohydrates_starch', 'carbohydrates_lactose', 'carbohydrates_sacchorose', 'carbohydrates_cellulose', 'dust_total', 'dust_nacl', 'apple_acid', 'na', 'k', 'ca', 'mg', 'p', 'fe', 'i', 'se', 'f', 'vitamin_a', 'vitamin_b_carotene', 'vitamin_b1', 'vitamin_b2', 'vitamin_pp', 'vitamin_c', 'vitamin_d', 'energy_kkal', 'energy_kdj'], 'number'],
            [['created_at'], 'safe'],
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
            'name' => 'Name',
            'products_category_id' => 'Products Category ID',
            'products_subcategory_id' => 'Products Subcategory ID',
            'sort' => 'Sort',
            'water' => 'Water',
            'protein' => 'Protein',
            'fat' => 'Fat',
            'carbohydrates_total' => 'Carbohydrates Total',
            'carbohydrates_saccharide' => 'Carbohydrates Saccharide',
            'carbohydrates_starch' => 'Carbohydrates Starch',
            'carbohydrates_lactose' => 'Carbohydrates Lactose',
            'carbohydrates_sacchorose' => 'Carbohydrates Sacchorose',
            'carbohydrates_cellulose' => 'Carbohydrates Cellulose',
            'dust_total' => 'Dust Total',
            'dust_nacl' => 'Dust Nacl',
            'apple_acid' => 'Apple Acid',
            'na' => 'Na',
            'k' => 'K',
            'ca' => 'Ca',
            'mg' => 'Mg',
            'p' => 'P',
            'fe' => 'Fe',
            'i' => 'I',
            'se' => 'Se',
            'f' => 'F',
            'vitamin_a' => 'Vitamin A',
            'vitamin_b_carotene' => 'Vitamin B Carotene',
            'vitamin_b1' => 'Vitamin B1',
            'vitamin_b2' => 'Vitamin B2',
            'vitamin_pp' => 'Vitamin Pp',
            'vitamin_c' => 'Vitamin C',
            'vitamin_d' => 'Vitamin D',
            'energy_kkal' => 'Energy Kkal',
            'energy_kdj' => 'Energy Kdj',
            'created_at' => 'Created At',
        ];
    }
}
