<?php

use common\models\Apple;
use yii\helpers\Html;

/** 
 * @var yii\web\View $this 
 * @var Apple[] $onTree
 * @var Apple[] $onGround
 * @var int $now
 * @var int $hour
 * @var int $day
 */

$this->title = 'Яблочки';
?>
<div class="apple-index">
    
    <?= $this->render('_svg'); ?>
    
    <h1><?= Html::encode($this->title) ?></h1>
    <div class="menu-container">
        <div class="menu">
            <div><?= Html::a('Сначала', ['reload'], ['class' => 'btn btn-primary']); ?></div>
            <div style="font-size:1.2em; font-weight:bold"><?= $day ?> день</div>
            <div style="font-size:1.2em; font-weight:bold"><?= $hour ?> час</div>
            <div><?= Html::a('+ час', ['next-time'], ['class' => 'btn btn-primary']); ?></div>
            <div><?= Html::a('+ день', ['next-day'], ['class' => 'btn btn-primary']); ?></div>
        </div>
    </div>
    <div id="workarea">
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

    <div id="alert"></div>
</div>
