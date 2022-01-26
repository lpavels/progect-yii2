<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "kids".
 *
 * @property int $id
 * @property int $user_id
 * @property string $last_name Фамилия
 * @property string $name Имя
 * @property int $federal_district_id
 * @property int $region_id
 * @property int $municipality_id
 * @property int $type_municipality
 * @property int $educational_institution_select Организация (есть/нет)
 * @property int $type_kids_id Тип ребёнка
 * @property string|null $name_organization Название организации
 * @property string $sex Пол
 * @property string $birth Возраст
 * @property string $height Рост
 * @property string $mass Масса
 * @property int|null $type_organiz_id
 * @property string $created_at
 * @property string|null $updated_at
 */
class KidsActivity21 extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'kids';
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
            [['user_id', 'last_name', 'name', 'federal_district_id', 'region_id', 'municipality_id', 'type_municipality', 'educational_institution_select', 'type_kids_id', 'sex', 'birth', 'height', 'mass'], 'required'],
            [['user_id', 'federal_district_id', 'region_id', 'municipality_id', 'type_municipality', 'educational_institution_select', 'type_kids_id', 'type_organiz_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['last_name', 'name', 'name_organization', 'sex', 'birth', 'height', 'mass'], 'string', 'max' => 250],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'last_name' => 'Last Name',
            'name' => 'Name',
            'federal_district_id' => 'Federal District ID',
            'region_id' => 'Region ID',
            'municipality_id' => 'Municipality ID',
            'type_municipality' => 'Type Municipality',
            'educational_institution_select' => 'Educational Institution Select',
            'type_kids_id' => 'Type Kids ID',
            'name_organization' => 'Name Organization',
            'sex' => 'Sex',
            'birth' => 'Birth',
            'height' => 'Height',
            'mass' => 'Mass',
            'type_organiz_id' => 'Type Organiz ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
    public function get_imt($heightVal, $massVal)
    {
        return round($massVal / (pow($heightVal / 100, 2)), 1);
    }

    public function get_imt2($heightVal, $massVal, $typeKidsVal, $sexVal, $birthVal, $status)
    {
        $imt_arrayVal = [
            'Дефицит массы тела',
            'Нормальный вес',
            'Избыточная масса тела',
            'Ожирение'
        ];
        $imt_boys_preschoolers = [
            1 => [14.5, 18.1, 18.5],
            2 => [14.5, 18.1, 18.5],
            3 => [14, 17.5, 17.8],
            4 => [13.9, 17, 17.3],
            5 => [13.8, 16.9, 17.2],
            6 => [13.5, 17, 17.5],
            7 => [13.6, 17.5, 18]
        ];
        $imt_boys_school = [
            6 => [13.5, 17, 17.5],
            7 => [13.6, 17.5, 18],
            8 => [13.8, 18, 18.5],
            9 => [13.9, 18.5, 19.5],
            10 => [14, 19.2, 20.4],
            11 => [14.3, 20, 21.3],
            12 => [14.7, 21, 22.1],
            13 => [15.1, 21.8, 23],
            14 => [15.6, 22.5, 23.9],
            15 => [16.3, 23.5, 24.7],
            16 => [16.9, 24, 25.4],
            17 => [17.3, 25, 26.1],
            18 => [17.9, 25.6, 26.9]
        ];
        $imt_boys_student = [
            17 => [17.3, 25, 26.1],
            18 => [17.9, 25.6, 26.9],
            19 => [18.2, 26.2, 27.8],
            20 => [18.6, 27, 28.4],
            21 => [18.6, 27, 28.4],
            22 => [18.6, 27, 28.4],
            23 => [18.6, 27, 28.4],
            24 => [18.6, 27, 28.4],
            25 => [18.6, 27, 28.4],
            26 => [18.6, 27, 28.4]
        ];
        $imt_boys_else = [
            1 => [14.5, 18.1, 18.5],
            2 => [14.5, 18.1, 18.5],
            3 => [14, 17.5, 17.8],
            4 => [13.9, 17, 17.3],
            5 => [13.8, 16.9, 17.2],
            6 => [13.5, 17, 17.5],
            7 => [13.6, 17.5, 18],
            8 => [13.8, 18, 18.5],
            9 => [13.9, 18.5, 19.5],
            10 => [14, 19.2, 20.4],
            11 => [14.3, 20, 21.3],
            12 => [14.7, 21, 22.1],
            13 => [15.1, 21.8, 23],
            14 => [15.6, 22.5, 23.9],
            15 => [16.3, 23.5, 24.7],
            16 => [16.9, 24, 25.4],
            17 => [17.3, 25, 26.1],
            18 => [17.9, 25.6, 26.9],
            19 => [18.2, 26.2, 27.8],
            20 => [18.6, 27, 28.4],
            21 => [18.6, 27, 28.4],
            22 => [18.6, 27, 28.4],
            23 => [18.6, 27, 28.4],
            24 => [18.6, 27, 28.4],
            25 => [18.6, 27, 28.4],
            26 => [18.6, 27, 28.4]
        ];
        $imt_girls_preschoolers = [
            1 => [14, 18, 18.2],
            2 => [14, 18, 18.2],
            3 => [13.8, 17.2, 17.4],
            4 => [13.5, 16.8, 17.1],
            5 => [13.3, 16.9, 17.2],
            6 => [13.2, 17, 17.3],
            7 => [13.2, 17.9, 18.4]
        ];
        $imt_girls_school = [
            6 => [13.2, 17, 17.3],
            7 => [13.2, 17.9, 18.4],
            8 => [13.2, 18.5, 18.7],
            9 => [13.5, 19, 19.6],
            10 => [13.9, 20, 21],
            11 => [14, 21, 22],
            12 => [14.5, 21.6, 23],
            13 => [15, 22.5, 24],
            14 => [15.5, 23.5, 24.8],
            15 => [16, 24, 25.5],
            16 => [16.5, 24.8, 26],
            17 => [16.9, 25.1, 26.8],
            18 => [17, 25.8, 27.3]
        ];
        $imt_girls_student = [
            17 => [16.9, 25.1, 26.8],
            18 => [17, 25.8, 27.3],
            19 => [17.2, 25.8, 27.8],
            20 => [17.5, 25.5, 28.2],
            21 => [17.5, 25.5, 28.2],
            22 => [17.5, 25.5, 28.2],
            23 => [17.5, 25.5, 28.2],
            24 => [17.5, 25.5, 28.2],
            25 => [17.5, 25.5, 28.2],
            26 => [17.5, 25.5, 28.2]
        ];
        $imt_girls_else = [
            1 => [14, 18, 18.2],
            2 => [14, 18, 18.2],
            3 => [13.8, 17.2, 17.4],
            4 => [13.5, 16.8, 17.1],
            5 => [13.3, 16.9, 17.2],
            6 => [13.2, 17, 17.3],
            7 => [13.2, 17.9, 18.4],
            8 => [13.2, 18.5, 18.7],
            9 => [13.5, 19, 19.6],
            10 => [13.9, 20, 21],
            11 => [14, 21, 22],
            12 => [14.5, 21.6, 23],
            13 => [15, 22.5, 24],
            14 => [15.5, 23.5, 24.8],
            15 => [16, 24, 25.5],
            16 => [16.5, 24.8, 26],
            17 => [16.9, 25.1, 26.8],
            18 => [17, 25.8, 27.3],
            19 => [17.2, 25.8, 27.8],
            20 => [17.5, 25.5, 28.2],
            21 => [17.5, 25.5, 28.2],
            22 => [17.5, 25.5, 28.2],
            23 => [17.5, 25.5, 28.2],
            24 => [17.5, 25.5, 28.2],
            25 => [17.5, 25.5, 28.2],
            26 => [17.5, 25.5, 28.2]
        ];

        $queteletIndexVal = $massVal / (pow($heightVal / 100, 2));

        if ($typeKidsVal == 1)
        {
            if ($sexVal == 0)
            {
                $arrayChild_temp = $imt_boys_preschoolers;
            }
            else $arrayChild_temp = $imt_girls_preschoolers;
        }
        if ($typeKidsVal == 2)
        {
            if ($sexVal == 0)
            {
                $arrayChild_temp = $imt_boys_school;
            }
            else $arrayChild_temp = $imt_girls_school;
        }
        if ($typeKidsVal == 3)
        {
            if ($sexVal == 0)
            {
                $arrayChild_temp = $imt_boys_student;
            }
            else $arrayChild_temp = $imt_girls_student;
        }
        if ($typeKidsVal == 4)
        {
            if ($sexVal == 0)
            {
                $arrayChild_temp = $imt_boys_else;
            }
            else $arrayChild_temp = $imt_girls_else;
        }

        if ($queteletIndexVal<$arrayChild_temp[$birthVal][0]) $arrayChildNum = 0;
        else if ($queteletIndexVal>=$arrayChild_temp[$birthVal][0]&& $queteletIndexVal <= $arrayChild_temp[$birthVal][1]) $arrayChildNum = 1;
        else if ($queteletIndexVal>$arrayChild_temp[$birthVal][1]&& $queteletIndexVal <= $arrayChild_temp[$birthVal][2]) $arrayChildNum = 2;
        else if ($queteletIndexVal>$arrayChild_temp[$birthVal][2]) $arrayChildNum = 3;

        if ($status == 1)
        {
            return $imt_arrayVal[$arrayChildNum];
        }
        if ($status == 2)
        {
            $minRecBodyMass = $arrayChild_temp[$birthVal][0] * (pow($heightVal / 100, 2));
            return round($minRecBodyMass,1);
        }
        if ($status == 3)
        {
            $maxRecBodyMass = $arrayChild_temp[$birthVal][1] * (pow($heightVal / 100, 2));
            return round($maxRecBodyMass,1);
        }
    }

    /*public function get_typeKids($id)
    {
        return TypeKids::findOne($id)->name;
    }*/
}
