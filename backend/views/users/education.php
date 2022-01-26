<?php

use common\models\QuestionsResponse;
use common\models\ReportTbl21;
use common\models\ReportTbl22;
use common\models\ThemeProgram;
use common\models\TrainingProgram;
use common\models\Trainings;
use common\models\TrainingThemes;
use common\models\User;
use yii\bootstrap4\Html;

$this->title = 'Данные по обучению';
$this->params['breadcrumbs'][] = $this->title;

$u_id = Yii::$app->user->id;
$queru = User::find()->where(['id' => $u_id])->one();
$training = TrainingProgram::find()->where(['id' => Yii::$app->user->identity->training_id])->one();

$date_last_attempt = QuestionsResponse::find()->select(['user_id', 'status', 'number_trying', 'creat_at'])->where(['user_id' => Yii::$app->user->identity->id, 'status' => 2])->orderBy(['number_trying' => SORT_DESC])->one()->creat_at; //дата прохождения последней попытки итогового теста
?>

<?php
$questions_vhod = QuestionsResponse::find()->where(['user_id' => $u_id, 'status' => 1])->one();
$questions_vihod = QuestionsResponse::find()->where(['user_id' => $u_id, 'status' => 2])->orderBy(['number_trying' => SORT_DESC])->one();

if (date('Y')===2021){
    $report_tbl = ReportTbl21::find()->where(['user_id' => $u_id])->one();
}elseif (date('Y')==2022){
    $report_tbl = ReportTbl22::find()->where(['user_id' => $u_id])->one();
    if (empty($report_tbl)){
        $report_tbl = ReportTbl21::find()->where(['user_id' => $u_id])->one();
    }
}

if (!empty($questions_vhod))
{
    $vhod = '<span class="text-success">Пройден</span>';
    $vhod_date = date('d.m.Y H:i:s', strtotime($questions_vhod->creat_at)) . ' (UTC+7)';
}
else
{
    $vhod = '<span class="text-danger">Не пройден</span>';
    $vhod_date = '';
}

if ($report_tbl->training_completed == 1)
{
    $vihod_check = 1;
    $vihod = '<span class="text-success">Пройден</span>';
    $vihod_date = date('d.m.Y H:i:s', strtotime($questions_vihod->creat_at)) . ' (UTC+7)';
}
elseif ($report_tbl->final_test == null || $report_tbl->final_test < 7)
{
    $vihod_check = 0;
    $vihod = '<span class="text-danger">Не пройден</span>';
    $vihod_date = date('d.m.Y H:i:s', strtotime($questions_vihod->creat_at)) . ' (UTC+7)';
}

if ($report_tbl->input_test == null)
{
    $vhod_count = '';
}
else
{
    $vhod_count = $report_tbl->input_test * 10 . '%';
}

if ($report_tbl->independent_work == null)
{
    $independent_work = '<span class="text-danger">Не пройдена</span>';
}
else
{
    $independent_work = '<span class="text-success">Пройдена</span>';
}

if ($report_tbl->final_test == null)
{
    $vihod_count = '';
}
else
{
    $vihod_count = $report_tbl->final_test * 10 . '%';
}

$my_themes_count = TrainingThemes::find()->where(['training_program_id' => Yii::$app->user->identity->training_id])->orderBy(['sort' => SORT_ASC])->count();
$my_themes = TrainingThemes::find()->where(['training_program_id' => Yii::$app->user->identity->training_id])->orderBy(['sort' => SORT_ASC])->all();

?>
<div class="">
    <h1 class="text-center"><?= Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="col-8 ml-3">
            <p class="mt-5"><b>Ваша программа обучения:</b> <?= $training->name ?></p>
            <p><b>Статус входного теста:</b> <?= $vhod ?></p>
            <p><b>Дата прохождения входного теста:</b> <?= $vhod_date ?> </p>

            <p>Процент правильных ответов входного теста: <?= $vhod_count ?></p>
            <div class="progress mb-3" style="height: 20px; width: 500px">
                <div class="progress-bar" role="progressbar" style="width: <?= $vhod_count ?>" aria-valuenow="25"
                     aria-valuemin="0" aria-valuemax="100"><h6><?= $vhod_count ?></h6></div>
            </div>
	    <p>Раздел "Основные задачи программы" находится в вкладке "Обучающие материалы".</p>
	    <?
            $count = 1;
            foreach ($my_themes as $my_theme)
            {
                $train = 'theme' . $count++;
                $training = '<span class="text-danger"> Не пройдена</span>';;
                if ($report_tbl->$train != null)
                {
                    $training = '<span class="text-success"> Пройдена</span>';
                }
                ?>
                <div class="mb-3"><b>Статус обучения
                        "<?= ThemeProgram::findOne($my_theme->theme_program_id)->short_name ?>":</b><?= $training ?>
                </div>
                <?
            }
            ?>

            <p><b>Статус выполнения самостоятельной работы:</b> <?= $independent_work ?> </p>

            <p><b>Статус итогового теста:</b> <?= $vihod ?></p>
            <?
            if (!empty($questions_vihod))
            {
                ?>
                <p><b>Дата прохождения итогового теста:</b> <?= $vihod_date ?> </p>
                <p><b>Попытка:</b> №<?= $questions_vihod->number_trying ?></p>
                <p>Процент правильных ответов итогового теста: <?= $vihod_count ?></p>
                <div class="progress mb-3" style="height: 20px; width: 500px">
                    <div class="progress-bar" role="progressbar" style="width: <?= $vihod_count ?>" aria-valuenow="25"
                         aria-valuemin="0" aria-valuemax="100"><h6><?= $vihod_count ?></h6></div>
                </div>
                <?
            }
            if ($vihod_check == 1)
            {
                /*echo Html::a('<span class="glyphicon glyphicon-asterisk"></span> Скачать сертификат в PDF формате',
                    ['certificate?id=' . $queru->id],
                    [
                        'class' => 'btn btn-outline-secondary mr-3 main-button-3',
                        'title' => Yii::t('yii', 'Вы можете сохранить данные в PDF формате'),
                        'data-toggle' => 'tooltip',
		]);*/
		
                echo Html::button(
                    '<span class="glyphicon glyphicon-asterisk"></span> Скачать сертификат в PDF формате',
                    ['class' => 'btn btn-outline-secondary mr-2 main-button-3 mt-1', 'id' => 'education']
	    );
		echo Html::a('<span class="glyphicon glyphicon-asterisk"></span> Открыть сертификат в PDF формате',
	            ['certificatetarget?id=' . $queru->id],
		    [
		        'class' => 'btn btn-outline-secondary main-button-3 mt-1',
		        'title' => Yii::t('yii', 'Вы можете сохранить данные в PDF формате'),
			'data-toggle' => 'tooltip',
			'target' =>'_blank',
		]);
            }
            ?>
        </div>
    </div>
</div>
<script>
    let id = <?= json_encode($queru->id);?>;
</script>
<?php
$script = <<< JS
var btn_pechat = $('#education');
btn_pechat.on('click', function () {
    $.ajax({
    url: 'certificate2',
    data: {'id': id},
    method: 'POST',
    dataType: 'JSON',
    success: function (data) {
        pdfMake.fonts = {
            myFont: {
                normal: 'Times_New_Roman.ttf',
                bold: 'Times_New_Roman_Bold.ttf',
                italics: 'Times_New_Roman_Italic.ttf',
                bolditalics: 'Times_New_Roman_Bold_Italic.ttf'
            }
        };
        var docDefinition = {
            background: {
                image: 'bee',
                width: 840
            },
            content: [
                {
                    text: '№ '+data['key_login'],
                    style: 'header',
                    alignment: 'center',
                    bold: false,
                    fontSize: 17,
                    margin: [0, 250, 0, 0]
                    //margin: [260, 240, 0, 0]
                },{
                    text: data['user_name'],
                    style: 'header',
                    fontSize: 28,
                    alignment: 'center',
                    margin: [0, 5, 0, 0]
                },{
                    text: 'прошел(а) обучение по санитарно-просветительской программе',
                    style: 'header',
                    fontSize: 20,
                    alignment: 'center',
                    bold: false,
                    margin: [0, 5, 0, 0]
                    //pageBreak:'after'
                },{
                    text: '«' + data['training_name'] + '»',
                    style: 'header',
                    alignment: 'center',
                    fontSize: 20,
                    bold: false,
                    margin: [0, 5, 0, 0]
                    //pageBreak:'after'
                },{
                    text: 'В объеме 15 часов',
                    style: 'header',
                    alignment: 'center',
                    fontSize: 10,
                    bold: false,
                    margin: [0, 8, 0, 0]
                    //pageBreak:'after'
                },{
                    text: 'НОВОСИБИРСК, ' + data['today'] + ' г.',
                    style: 'header',
                    alignment: 'center',
                    fontSize: 11,
                    bold: true,
                    margin: [0, 95, 0, 0],
                    color: '#725745'
                    //color: '#725745'
                    //pageBreak:'after'
                },
            ],
            images: {
                bee: testImageDataUrl,
            },
            pageOrientation: 'landscape',
            defaultStyle: {
                font: 'myFont'
            },
            styles: {
                header: {
                    fontSize: 20,
                    bold: true,
                    alignment: 'justify'
                },
            }
        };
        pdfMake.createPdf(docDefinition).download(data['user_name']);
    },
    });
});
JS;
$this->registerJs($script, yii\web\View::POS_READY);

$this->registerJsFile(
    '@web/js/pdfmake/pdfmake.min.js',
    [
        'depends' => 'yii\web\YiiAsset', // зависимости для скрипта
        'position' => $this::POS_HEAD    // подключать в <head>
    ]
);
$this->registerJsFile(
    '@web/js/pdfmake/vfs_fonts.js',
    [
        'depends' => 'yii\web\YiiAsset', // зависимости для скрипта
        'position' => $this::POS_HEAD    // подключать в <head>
    ]
);
$this->registerJsFile(
    '@web/js/main.js',
    [
        'depends' => 'yii\web\YiiAsset', // зависимости для скрипта
        'position' => $this::POS_HEAD    // подключать в <head>
    ]
);
