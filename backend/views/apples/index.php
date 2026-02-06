<?php

use backend\models\Apple;
use yii\helpers\Html;

/** 
 * @var yii\web\View $this 
 * @var Apple[] $onTree
 * @var Apple[] $onGround
 * @var int $start
 * @var int $now
 */

$diff = $now - $start;
$day = floor($diff / (60 * 60 * 24));
$hour = floor($diff / (60 * 60));

$this->title = 'Яблочки';
?>
<div class="apple-index">
    
    <?= $this->render('_svg'); ?>
    
    <h1><?= Html::encode($this->title) ?></h1>
    <div class="menu-container">
        <ul class="menu">
            <li><?= Html::a('Сначала', ['reload'], ['class' => 'btn btn-primary']); ?></li>
            <li>Старт: <?= date("H:i d.m.Y", $start) ?></li>
            <li>На календаре <?= date("H:i d.m.Y", $now) ?></li>
            <li style="font-size:1.2em; font-weight:bold"><?= $day ?> день</li>
            <li style="font-size:1.2em; font-weight:bold"><?= $hour ?> час</li>
            <li><?= Html::a('+ ' . Apple::HOURS_INCREMENT . ' час', ['next-time'], ['class' => 'btn btn-primary time-button', 'data-action' => 'next-time']); ?></li>
            <li><?= Html::a('+ день', ['next-day'], ['class' => 'btn btn-primary time-button', 'data-action' => 'next-day']); ?></li>
        </ul>
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
