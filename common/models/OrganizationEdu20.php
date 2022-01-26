<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "organization".
 *
 * @property int $id
 * @property string|null $title
 * @property string|null $short_title
 * @property string|null $address
 * @property int|null $federal_district_id
 * @property int|null $region_id
 * @property int|null $type_org
 * @property int|null $type_lager_id
 * @property int|null $municipality_id
 * @property string|null $phone
 * @property string|null $email
 * @property string|null $inn
 * @property int|null $organizator_food
 * @property int|null $medic_service_programm
 * @property string|null $org_balansodergatel
 * @property int|null $forma_sobstvennosti_id
 * @property string|null $sez_build
 * @property int|null $date_sez_build
 * @property string|null $sez_med
 * @property int|null $date_sez_med
 * @property int|null $regim_id
 * @property int|null $moshnost_lager_leto
 * @property int|null $moshnost_lager_inoe
 * @property int|null $status
 * @property string|null $created_at
 */
class OrganizationEdu20 extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'organization';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('db_edu20');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title'], 'string'],
            [['federal_district_id', 'region_id', 'type_org', 'type_lager_id', 'municipality_id', 'organizator_food', 'medic_service_programm', 'forma_sobstvennosti_id', 'date_sez_build', 'date_sez_med', 'regim_id', 'moshnost_lager_leto', 'moshnost_lager_inoe', 'status'], 'integer'],
            [['created_at'], 'safe'],
            [['short_title', 'address', 'phone', 'email', 'inn', 'org_balansodergatel', 'sez_build', 'sez_med'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'short_title' => 'Short Title',
            'address' => 'Address',
            'federal_district_id' => 'Federal District ID',
            'region_id' => 'Region ID',
            'type_org' => 'Type Org',
            'type_lager_id' => 'Type Lager ID',
            'municipality_id' => 'Municipality ID',
            'phone' => 'Phone',
            'email' => 'Email',
            'inn' => 'Inn',
            'organizator_food' => 'Organizator Food',
            'medic_service_programm' => 'Medic Service Programm',
            'org_balansodergatel' => 'Org Balansodergatel',
            'forma_sobstvennosti_id' => 'Forma Sobstvennosti ID',
            'sez_build' => 'Sez Build',
            'date_sez_build' => 'Date Sez Build',
            'sez_med' => 'Sez Med',
            'date_sez_med' => 'Date Sez Med',
            'regim_id' => 'Regim ID',
            'moshnost_lager_leto' => 'Moshnost Lager Leto',
            'moshnost_lager_inoe' => 'Moshnost Lager Inoe',
            'status' => 'Status',
            'created_at' => 'Created At',
        ];
    }
}
