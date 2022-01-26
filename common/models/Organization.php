<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "organization".
 *
 *
 * @property int $id
 * @property string $title
 * @property string $short_title
 * @property string $address
 * @property int $federal_district_id
 * @property string $type_org
 * @property int $region_id
 * @property string $municipality_id
 * @property string $phone
 * @property string $email
 * @property string $inn
 * @property int $organizator_food
 * @property int $medic_service_programm
 * @property int $status
 * @property string $created_at
 */
class Organization extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'organization';
    }

    public function rules()
    {
        return [
            [['title', 'federal_district_id', 'region_id', 'municipality', 'type_org'], 'required'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Наименование организации',
            'type_org' => 'Тип организации',
            'short_title' => 'Короткое название организации',
            'address' => 'Юридический адрес',
            'federal_district_id' => 'Федеральный округ',
            'region_id' => 'Субъект федерации',
            'municipality_id' => 'Муниципальное образование',
            'phone' => 'Телефон',
            'email' => 'Электронная почта',
            'inn' => 'ИНН',
            'organizator_food' => 'Организатор питания',
            'medic_service_programm' => 'Настройка программы \'Мед. обслуживание\'',
            'status' => 'Статус',
            'type_lager_id' => 'Тип лагеря',
            'org_balansodergatel' => 'Наименование организации балансодержателя (полностью)',
            'forma_sobstvennosti_id' => 'Форма собственности',
            'sez_build' => 'Наличие положительного санитарно-эпидемиологического заключения на здания, строения и сооружения',
            'date_sez_build' => 'Дата выдачи СЭЗ',
            'sez_med' => 'Наличие положительного санитарно-эпидемиологического заключения на медицинскую деятельность',
            'date_sez_med' => 'Дата выдачи СЭЗ на мед. деят',
            'regim_id' => 'Режим работы',
            'moshnost_lager_leto' => 'Мощность лагеря в смену (лето)',
            'moshnost_lager_inoe' => 'Мощность лагеря в смену (иные смены)',
            'created_at' => 'Дата создания',
        ];
    }

    /*public function beforeSave($insert){
        if (parent::beforeSave($insert)) {
            if(Yii::$app->user->can('camp_director'))
            {
                $this->date_sez_med = strtotime($this->date_sez_med);
                $this->date_sez_build = strtotime($this->date_sez_build);
                return true;
            }
            return true;
        }
        return false;
    }*/

    public function get_district($id)
    {
        $district = FederalDistrict::findOne($id);
        $district = $district->name;
        return $district;
    }

    public function get_region($id)
    {
        $region = Region::find()->where(['id' => $id])->one();
        $region = $region->name;
        return $region;
    }
    public function get_municipality($id)
    {
        $region = Municipality::find()->where(['id' => $id])->one();
        $region = $region->name;
        return $region;
    }
    public function get_type_org($id)
    {
        $items = [
            '1' => 'Общеобразовательная организация',
            '2' => 'Организация дошкольного образования',
            '3' => 'Организация профессионального образования',
            '4' => 'Организация дополнительного образования',
            '5' => 'Медицинская организация',
            '6' => 'Организация социального обслуживания',
            '8' => 'Физкультурно-спортивная организация',
            '9' => 'Иная'
        ];

        $typeOrg = $items[$id];
        return $typeOrg;
    }

    /*public function get_type_name_organization($id)
    {
        $type = TypeOrganization::find()->where(['id' => $id])->one();
        $type = $type->name;
        return $type;
    }
    */

    /*public function ball_response($id_u)
    {
        //$id_u - ����� ������������
        //$questions - ���������� �������� �����
        //

        $questions = \common\models\QuestionsResponse::find()->where(['user_id'=>$id_u])->all();
        $caunt = 0;
        foreach ($questions as $question){
            $variant = \common\models\QuestionsVariant::find()->where(['id'=>$question->questions_variant_id])->one();
            if($variant->correct == '1'){
                $caunt++;
            }
        }
        return $caunt;
    }*/

    /*public function ball_response_fin($id_u)
    {
        //$id_u - ����� ������������
        //$questions - ���������� �������� �����
        //

        $questions = \common\models\QuestionsResponseFin::find()->where(['user_id'=>$id_u])->all();
        $caunt = 0;
        foreach ($questions as $question){
            $variant = \common\models\QuestionsVariant::find()->where(['id'=>$question->questions_variant_id])->one();
            if($variant->correct == '1'){
                $caunt++;
            }
        }
        return $caunt;
    }*/
}
