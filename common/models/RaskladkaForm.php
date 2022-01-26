<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\rbac\DbManager;

/**
 * LoginForm is the model behind the login form.
 *
 * @property User|null $user This property is read-only.
 *
 */
class RaskladkaForm extends Model
{
    public $data;
    public $brutto_netto;
    public $menu1;
    public $count1;
    public $menu2;
    public $count2;
    public $menu3;
    public $count3;
    public $menu4;
    public $count4;
    public $menu5;
    public $count5;
    public $menu6;
    public $count6;
    public $menu7;
    public $count7;
    public $menu8;
    public $count8;
    public $menu9;
    public $count9;
    public $menu10;
    public $count10;
    public $menu11;
    public $count11;
    public $menu12;
    public $count12;
    public $menu13;
    public $count13;
    public $menu14;
    public $count14;
    public $menu15;
    public $count15;
    public $menu16;
    public $count16;
    public $menu17;
    public $count17;
    public $menu18;
    public $count18;
    public $menu19;
    public $count19;
    public $menu20;
    public $count20;
    public $menu21;
    public $count21;
    public $menu22;
    public $count22;
    public $menu23;
    public $count23;
    public $menu24;
    public $count24;
    public $menu25;
    public $count25;
    public $menu26;
    public $count26;
    public $menu27;
    public $count27;
    public $menu28;
    public $count28;
    public $menu29;
    public $count29;
    public $menu30;
    public $count30;
    public $menu31;
    public $count31;
    public $menu32;
    public $count32;
    public $menu33;
    public $count33;
    public $menu34;
    public $count34;
    public $menu35;
    public $count35;
    public $menu36;
    public $count36;
    public $menu37;
    public $count37;
    public $menu38;
    public $count38;
    public $menu39;
    public $count39;
    public $menu40;
    public $count40;
    public $menu41;
    public $count41;
    public $menu42;
    public $count42;
    public $menu43;
    public $count43;
	
	
	public $menu44;
    public $count44;
    public $menu45;
    public $count45;
    public $menu46;
    public $count46;
    public $menu47;
    public $count47;
    public $menu48;
    public $count48;
    public $menu49;
    public $count49;
    public $menu50;
    public $count50;
    public $menu51;
    public $count51;
    public $menu52;
    public $count52;

    public $menu53;
    public $count53;
    public $menu54;
    public $count54;
    public $menu55;
    public $count55;
    public $menu56;
    public $count56;
    public $menu57;
    public $count57;
    public $menu58;
    public $count58;
    public $menu59;
    public $count59;
    public $menu60;
    public $count60;
    public $menu61;
    public $count61;

    public $menu62;
    public $count62;
    public $menu63;
    public $count63;
    public $menu64;
    public $count64;
	
	public $menu65;
    public $count65;
    public $menu66;
    public $count66;
    public $menu67;
    public $count67;
    public $menu68;
    public $count68;
    public $menu69;
    public $count69;
    public $menu70;
    public $count70;
    public $menu71;
    public $count71;
    public $menu72;
    public $count72;
    public $menu73;
    public $count73;
	
	public $menu74;
    public $count74;
    public $menu75;
    public $count75;
    public $menu76;
    public $count76;
    public $menu77;
    public $count77;
    public $menu78;
    public $count78;
    public $menu79;
    public $count79;
    public $menu80;
    public $count80;
    public $menu81;
    public $count81;
    public $menu82;
    public $count82;

    public $menu83;
    public $count83;
    public $menu84;
    public $count84;
    public $menu85;
    public $count85;
    public $menu86;
    public $count86;
    public $menu87;
    public $count87;
    public $menu88;
    public $count88;
    public $menu89;
    public $count89;
    public $menu90;
    public $count90;
    public $menu91;
    public $count91;

    public $menu92;
    public $count92;
    public $menu93;
    public $count93;
    public $menu94;
    public $count94;
    public $menu95;
    public $count95;
    public $menu96;
    public $count96;
    public $menu97;
    public $count97;
    public $menu98;
    public $count98;
    public $menu99;
    public $count99;
    public $menu100;
    public $count100;

    public $menu101;
    public $count101;
    public $menu102;
    public $count102;
    public $menu103;
    public $count103;
    public $menu104;
    public $count104;
    public $menu105;
    public $count105;
    public $menu106;
    public $count106;
    public $menu107;
    public $count107;
    public $menu108;
    public $count108;
    public $menu109;
    public $count109;

    public $menu110;
    public $count110;
    public $menu111;
    public $count111;
    public $menu112;
    public $count112;
    public $menu113;
    public $count113;
    public $menu114;
    public $count114;
    public $menu115;
    public $count115;
    public $menu116;
    public $count116;
    public $menu117;
    public $count117;
    public $menu118;
    public $count118;

    public $menu119;
    public $count119;
    public $menu120;
    public $count120;
    public $menu121;
    public $count121;
    public $menu122;
    public $count122;
    public $menu123;
    public $count123;
    public $menu124;
    public $count124;
    public $menu125;
    public $count125;
    public $menu126;
    public $count126;
    public $menu127;
    public $count127;
    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['data', 'brutto_netto'], 'required'],
            [['brutto_netto', 'count',
                '$menu1', '$count1',
                '$menu2', '$count2',
                '$menu3', '$count3',
                '$menu4', '$count4',
                '$menu5', '$count5',
                '$menu6', '$count6',
                '$menu7', '$count7',
                '$menu8', '$count8',
                '$menu9', '$count9',
                '$menu10', '$count10',
                '$menu11', '$count11',
                '$menu12', '$count12',
                '$menu13', '$count13',
                '$menu14', '$count14',
                '$menu15', '$count15',
                '$menu16', '$count16',
                '$menu17', '$count17',
                '$menu18', '$count18',
                '$menu19', '$count19',
                '$menu20', '$count20',
                '$menu21', '$count21',
                '$menu22', '$count22',
                '$menu23', '$count23',
                '$menu24', '$count24',
                '$menu25', '$count25',
                '$menu26', '$count26',
                '$menu27', '$count27',
                '$menu28', '$count28',
                '$menu29', '$count29',
                '$menu30', '$count40',
                '$menu31', '$count31',
                '$menu32', '$count32',
                '$menu33', '$count33',
                '$menu34', '$count34',
                '$menu35', '$count35',
                '$menu36', '$count36',
                'menu37', 'count37',
                'menu38', 'count38',
                'menu39', 'count39',
            ], 'integer'],
            [['netto'], 'double']

        ];
    }

    public function attributeLabels()
    {
        return [
            'data' => 'Дата',
            'brutto_netto' => 'Брутто/Нетто',
            'menu1' => 'Меню',
            'count1' => 'Количество',
            'menu2' => 'Меню',
            'count2' => 'Количество',
            'menu3' => 'Меню',
            'count3' => 'Количество',
            'menu4' => 'Меню',
            'count4' => 'Количество',
            'menu5' => 'Меню',
            'count5' => 'Количество',
            'menu6' => 'Меню',
            'count6' => 'Количество',
            'menu7' => 'Меню',
            'count7' => 'Количество',
        ];
    }
}
