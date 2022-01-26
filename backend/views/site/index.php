<?php

/* @var $this yii\web\View */

use common\models\User;
use yii\web\View;

$this->title = 'Основы здорового питания';
?>

<?php
if (Yii::$app->user->can('admin'))
{
    $model = new User();
    $registrations = $model->chartRegistrations();
}
?>

<div class="site-index">
    <div class="title text-center">Новости</div>

    <div class="news">
        <? foreach ($news as $new){?>
            <div class="news-block">
                <div class="news-line">
                    <div class="news-content container">
                        <div class="news-title"><?=($new->fix==1) ? '<i class="fa fa-thumb-tack"></i> ' . $new->title : $new->title?></div>
                        <div class="news-text"><?=$new->news_text ?></div>
                        <div class="news-date"><?=date('d.m.Y',strtotime($new->created_at)) ?></div>
                    </div>
                </div>
                <? if($new->category == 0){?>
                    <img src="../images/icons/icon-news.jpg" alt="picture">
                <?}elseif ($new->category == 1){?>
                    <img src="../images/icons/icon-video.jpg" alt="picture">
                <?}elseif ($new->category == 2){?>
                    <img src="../images/icons/icon-attention.jpg" alt="picture">
                <?}?>
            </div>
        <?}?>
    </div>

    <div class="body-content">
        <?
        if (Yii::$app->user->can('admin2'))
        {
            ?>
            <div class="row">
                <div class="col-6">
                    <canvas id="registrations"></canvas>
                </div>
                <div class="col-6">
                    <canvas id="completed"></canvas>
                </div>
            </div>
            <script>
                var ctx = document.getElementById('registrations').getContext('2d');
                var myChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: <?php echo json_encode(array_reverse($registrations[0][0]));?>,
                        datasets: [{
                            label: '# of Votes',
                            data: <?php echo json_encode(array_reverse($registrations[0][1]));?>,
                            backgroundColor: [
                                'rgba(255, 99, 132, 0.2)',
                                'rgba(54, 162, 235, 0.2)',
                                'rgba(255, 206, 86, 0.2)',
                                'rgba(75, 192, 192, 0.2)',
                                'rgba(153, 102, 255, 0.2)',
                                'rgba(255, 159, 64, 0.2)',
                                'rgba(0, 255, 180, 0.2)',
                                'rgba(207, 138, 192, 0.2)',
                                'rgba(238, 130, 238, 0.2)',
                                'rgba(60, 179, 113, 0.2)'
                            ],
                            borderColor: [
                                'rgba(255, 99, 132, 1)',
                                'rgba(54, 162, 235, 1)',
                                'rgba(255, 206, 86, 1)',
                                'rgba(75, 192, 192, 1)',
                                'rgba(153, 102, 255, 1)',
                                'rgba(255, 159, 64, 1)',
                                'rgba(0, 255, 180, 1)',
                                'rgba(207, 138, 192, 1)',
                                'rgba(238, 130, 238, 1)',
                                'rgba(60, 179, 113, 1)'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            yAxes: [{
                                ticks: {
                                    beginAtZero: true
                                }
                            }]
                        }
                    }
                });
            </script>
            <?
        }
        ?>
    </div>
</div>


<?php
$js = <<< JS
   
JS;
$this->registerJs($js, View::POS_READY);
$this->registerCssFile('@web/css/adaptive.css');