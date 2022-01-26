<?php

use yii\helpers\Html;
use yii\web\View;

echo 'Дошкольная программа ver.2';
echo '<br>Start user id: ' . $a;
echo '<br>End user id: ' . $b;
echo '<br>Пользователей обновлено: ' . $u_editCount;
echo '<br>Пользователей без входного теста: ' . $u_skipCount;
echo '<br>Выполнено за ' . $timeComplete . ' секунд';
echo '<br>Начато выполнение в: ' . $timeStart;
echo '<br>Окончание выполнение в: ' . $timeEnd;

$a= $a + 10;
$b= $b + 10;

if ($a<=$usersCount) {
    $ffdsf = Html::a('Перейти', 'calculation-preschool-version-second?a='.$a.'&b='.$b, ['class'=>'btn btn-lg btn-primary', 'id'=>'clickbtn']);
}
else{
    $ffdsf = 'Выполнение завершено';
}

echo '<br>';
echo $ffdsf;

?>

    <script>
        window.onload = function() {
            clickButton();
        };
        function clickButton() {
            document.querySelector('#clickbtn').click();
        }
        //setInterval(clickButton, 1000);
    </script>

<?php
$js = <<< JS
    
JS;
$this->registerJs($js, View::POS_READY);