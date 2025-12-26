<?php

use common\models\Apple;
use yii\helpers\Html;

/** 
 * @var yii\web\View $this 
 * @var Apple[] $onTree
 * @var Apple[] $onGround
 */

$this->title = 'Apples';
?>
<div class="apple-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= Html::a('Вырастить яблоки', ['cultivate'], ['class' => 'btn btn-success', ['confirm' => 'Вырастить все яблоки заново?']]); ?>
    <?= Html::a('Следующий день', ['nextDay'], ['class' => 'btn btn-primary']); ?>

    <div id="tree">
        <?php foreach ($onTree as $appleOnTree) {
            echo $this->render('_apple', ['model' => $appleOnTree]);
        } ?>
    </div>
    <div id="ground">
        <?php foreach ($onGround as $appleOnGround) {
            echo $this->render('_apple', ['model' => $appleOnGround]);
        } ?>
    </div>    

</div>
