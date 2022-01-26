<?php

use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;

?>

<div class="report-rpn container">
    <div class="row">
        <div class="text-center"><h4>Отчёт по самостоятельной работе</h4></div>
        <?php
        $form = ActiveForm::begin(); ?>
        <div class="col">
            <?= $form->field($model_report, 'report_federal_district')->dropDownList(
                $district_item,
                [
                    'options' => [$post['report_federal_district'] => ['Selected' => true]],
                ]
            )->label(false); ?>
        </div>

        <div class="row">
            <div class="form-group" style="margin: 0 auto">
                <?= Html::submitButton(
                    'Посмотреть',
                    ['value' => 'view', 'class' => 'mt-2 btn main-button-3 beforeload']
                ) ?>
                <button class="btn main-button-3 mt-2 load" type="button" disabled style="display: none">
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    Пожалуйста, подождите...
                </button>
            </div>
        </div>

        <?php
        ActiveForm::end(); ?>
    </div>
</div>

<?php
if ($show == 1) { ?>
    <div class="rep-child table-responsive">
        <table class="table table-sm table-bordered table-hover text-center">
            <caption>Отчёт по самостоятельной работе</caption>
            <thead class="table-info">
            <tr>
                <td rowspan="2">№</td>
                <td rowspan="2">Федеральный округ</td>
                <td rowspan="2">Субъект федерации</td>
                <td rowspan="2">Муниципальное образование</td>
                <td rowspan="2">Организация</td>
                <td rowspan="2">Идентификационный номер</td>
                <td rowspan="2">Тип слушателя</td>
                <td rowspan="2">Пол</td>
                <td rowspan="2">Год рождения</td>
                <td rowspan="2">Возраст (лет)</td>
                <td rowspan="2">Рост (см)</td>
                <td rowspan="2">Масса (кг)</td>

                <td rowspan="2">ИМТ</td>
                <td rowspan="2">Физическое развитие</td>
                <td colspan="4">Суточные энерготраты (ккал)</td>
                <td colspan="4">Суточные энерготраты (%)</td>

                <td rowspan="2">ДА < 40%</td>
                <td rowspan="2">ДА < 30%</td>

                <td rowspan="2">Занимается ли в кружке (студии) да/нет</td>
                <td rowspan="2">Занимается ли в спортивной секции - да/нет</td>
                <td rowspan="2">Нигде не занимается</td>

                <td rowspan="2">Использование сотового телефона во время перемен - да/нет</td>
                <td rowspan="2">Рекомендуемые суточные энерготраты (ккал)</td>
                <td rowspan="2">Суточные энерготраты в сравнении с рекомендуемыми (выше, ниже, соответствуют)</td>
                <td rowspan="2">% не соотвествия (+/-)</td>
                <td rowspan="2">количество приемов пищи</td>
                <td colspan="4">масса съеденной за день пищи (гр)</td>
                <td colspan="4">удельный вес массы пищи съеденной за день (в%)</td>
                <td colspan="4">количество белка за сутки (в гр)</td>
                <td colspan="4">Удельный вес белков в общей калорийности (в%)</td>
                <td colspan="4">Содержание (суммарное витаминов и минералов) на 1 кг пищи (мг)</td>
                <td colspan="4">калорийность 1 кг съеденной пищи (ккал)</td>
                <td colspan="4">удельный вес калорийности съеденной пищи (%)</td>
                <td colspan="4">Удельный вес белков на 1 кг (в%)</td>
                <td rowspan="2">соответствие калорийности пищи суточным энерготратам (выше, ниже, соответствуют)</td>
                <td rowspan="2">% не соотвествия (+/-)</td>
                <td rowspan="2">соответствие возрастным нормативам (выше, ниже, соответствуют)</td>
                <td rowspan="2">% не соотвествия (+/-)</td>

                <td rowspan="2">Калорийность за день</td>
                <td rowspan="2">Возрастной норматив</td>
                <td rowspan="2">Суточные энерготраты</td>
            </tr>
            <tr>
                <td>Всего</td>
                <td>ОО</td>
                <td>СДДП</td>
                <td>ДА</td>
                <td>Всего</td>
                <td>ОО</td>
                <td>СДДП</td>
                <td>ДА</td>

                <td>Всего</td>
                <td>В школе</td>
                <td>Дома</td>
                <td>На улице</td>
                <td>Всего</td>
                <td>В школе</td>
                <td>Дома</td>
                <td>На улице</td>
                <td>Всего</td>
                <td>В школе</td>
                <td>Дома</td>
                <td>На улице</td>
                <td>Всего</td>
                <td>В школе</td>
                <td>Дома</td>
                <td>На улице</td>
                <td>Всего</td>
                <td>В школе</td>
                <td>Дома</td>
                <td>На улице</td>
                <td>Всего</td>
                <td>В школе</td>
                <td>Дома</td>
                <td>На улице</td>
                <td>Всего</td>
                <td>В школе</td>
                <td>Дома</td>
                <td>На улице</td>
                <td>Всего</td>
                <td>В школе</td>
                <td>Дома</td>
                <td>На улице</td>
            </tr>
            <tr>
                <td>1</td>
                <td>2</td>
                <td>3</td>
                <td>4</td>
                <td>5</td>
                <td>6</td>
                <td>7</td>
                <td>8</td>
                <td>9</td>
                <td>10</td>
                <td>11</td>
                <td>12</td>
                <td>13</td>
                <td>14</td>
                <td>15</td>
                <td>16</td>
                <td>17</td>
                <td>18</td>
                <td>19</td>
                <td>20</td>
                <td>21</td>
                <td>22</td>
                <td>23</td>
                <td>24</td>
                <td>25</td>
                <td>26</td>
                <td>27</td>
                <td>28</td>
                <td>29</td>
                <td>30</td>
                <td>31</td>
                <td>32</td>
                <td>33</td>
                <td>34</td>
                <td>35</td>
                <td>36</td>
                <td>37</td>
                <td>38</td>
                <td>39</td>
                <td>40</td>
                <td>41</td>
                <td>42</td>
                <td>43</td>
                <td>44</td>
                <td>45</td>
                <td>46</td>
                <td>47</td>
                <td>48</td>
                <td>49</td>
                <td>50</td>
                <td>51</td>
                <td>52</td>
                <td>53</td>
                <td>54</td>
                <td>55</td>
                <td>56</td>
                <td>57</td>
                <td>58</td>
                <td>59</td>
                <td>60</td>
                <td>61</td>
                <td>62</td>
                <td>63</td>
                <td>64</td>

                <td>65</td>
                <td>66</td>
                <td>67</td>
                <td>68</td>
                <td>69</td>
                <td>70</td>
                <td>71</td>
            </tr>
            </thead>
            <tbody>
            <?php
            $count = 1;
            foreach ($data as $item) {
                $imt_arr = $model_report->imt(
                    $item['mass'],
                    $item['height'],
                    $item['sex'],
                    $item['age'],
                    $item['field18'],
                    $item['field19'],
                    $item['sleep_day'],
                    $item['field17'],
                    $item['charging'],
                    $item['field2'],
                    $item['walk'],
                    $item['field15'],
                    $item['additional_education'],
                    $item['field4'],
                    $item['sports_section'],
                    $item['sports_section1'],
                    $item['field6'],
                    $item['sports_section2'],
                    $item['field7'],
                    $item['field8'],
                    $item['field9'],
                    $item['field20'],
                    $item['field21'],
                    $item['use_telephone'],
                    $item['menu_id']
                );
                $nutrition = $model_report->get_day_protein_vitamin_mineral(
                    $item['menu_id'],
                    $item['age'],
                    $imt_arr[5]
                );
                ?>
                <tr>
                    <td><?= $count++ ?></td>
                    <td><?= $item['federal_district_name'] ?></td>
                    <td><?= $item['region_name'] ?></td>
                    <td><?= $item['municipality_name'] ?></td>
                    <td><?= $item['organization_name'] ?></td>
                    <td><?= $item['key_login'] ?></td>
                    <td><?= $item['type_listener'] ?></td>
                    <td><?= $sex_arr[$item['sex']] ?></td>
                    <td><?= $item['year_birth'] ?></td>
                    <td><?= $item['age'] ?></td>
                    <td><?= $item['height'] ?></td>
                    <td><?= $item['mass'] ?></td>

                    <td><?= $imt_arr[0] ?></td>
                    <td><?= $imt_arr[1] ?></td>
                    <td><?= $imt_arr[5] ?></td>
                    <td><?= $imt_arr[2] ?></td>
                    <td><?= $imt_arr[3] ?></td>
                    <td><?= $imt_arr[4] ?></td>

                    <td><?= $imt_arr[6] ?></td> <!-- Суточные энерготраты (%) -->
                    <td><?= $imt_arr[7] ?></td> <!-- Суточные энерготраты (%) -->
                    <td><?= $imt_arr[8] ?></td> <!-- Суточные энерготраты (%) -->
                    <td><?= $imt_arr[9] ?></td> <!-- Суточные энерготраты (%) -->

                    <td><?= ($imt_arr[9] < 40) ? 1 : 0 ?></td>
                    <td><?= ($imt_arr[9] < 30) ? 1 : 0 ?></td>

                    <td><?= $item['additional_education'] ?></td>
                    <td><?= $item['sports_section'] ?></td>

                    <!-- Нигде не занимается -->
                    <?php
                    if ($item['additional_education'] == 0 && $item['sports_section'] == 0) {
                        ?>
                        <td>1</td>
                        <?php
                    } else {
                        ?>
                        <td>0</td>
                        <?php
                    } ?>

                    <td><?= $item['use_telephone'] ?></td>
                    <td><?= $imt_arr[10] ?></td>
                    <td><?= $imt_arr[11] ?></td>
                    <td><?= $imt_arr[12] ?></td>
                    <td><?= $item['food_intake'] ?></td>

                    <td><?= $nutrition[0] ?></td>
                    <td><?= $nutrition[1] ?></td>
                    <td><?= $nutrition[2] ?></td>
                    <td><?= $nutrition[3] ?></td>
                    <td><?= $nutrition[4] ?></td>
                    <td><?= $nutrition[5] ?></td>
                    <td><?= $nutrition[6] ?></td>
                    <td><?= $nutrition[7] ?></td>
                    <td><?= $nutrition[8] ?></td>
                    <td><?= $nutrition[9] ?></td>
                    <td><?= $nutrition[10] ?></td>
                    <td><?= $nutrition[11] ?></td>

                    <td><?= $nutrition[12] ?></td>
                    <td><?= $nutrition[13] ?></td>
                    <td><?= $nutrition[14] ?></td>
                    <td><?= $nutrition[15] ?></td>

                    <td><?= $nutrition[16] ?></td> <!--Содержание (суммарное витаминов и минералов) на 1 кг пищи (мг)-->
                    <td><?= $nutrition[17] ?></td> <!--Содержание (суммарное витаминов и минералов) на 1 кг пищи (мг)-->
                    <td><?= $nutrition[18] ?></td> <!--Содержание (суммарное витаминов и минералов) на 1 кг пищи (мг)-->
                    <td><?= $nutrition[19] ?></td> <!--Содержание (суммарное витаминов и минералов) на 1 кг пищи (мг)-->

                    <td><?= $nutrition[20] ?></td>
                    <td><?= $nutrition[21] ?></td>
                    <td><?= $nutrition[22] ?></td>
                    <td><?= $nutrition[23] ?></td>

                    <td><?= $nutrition[24] ?></td>
                    <td><?= $nutrition[25] ?></td>
                    <td><?= $nutrition[26] ?></td>
                    <td><?= $nutrition[27] ?></td>
                    <td><?= $nutrition[28] ?></td>
                    <td><?= $nutrition[29] ?></td>
                    <td><?= $nutrition[30] ?></td>
                    <td><?= $nutrition[31] ?></td>

                    <td><?= $nutrition[36] ?></td>
                    <td><?= empty(!$nutrition[37]) ? round(($nutrition[37] / $imt_arr[5] * 100) - 100, 1) : 0 ?></td>
                    <!--100/ ( двигательная активность(ккал)/питание(ккал) )-100 -->
                    <td><?= $nutrition[38] ?></td>
                    <td><?= $nutrition[39] ?></td>

                    <td><?= round($nutrition[37], 1) ?></td> <!--калорийность за день-->
                    <td><?= $nutrition[40] ?></td>
                    <td><?= $imt_arr[5] ?></td>
                </tr>
                <?php
            } ?>
            </tbody>
        </table>
    </div>
    <?php
} ?>


<pre>

</pre>