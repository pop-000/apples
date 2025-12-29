<?php

use yii\helpers\Html;

/** @var yii\web\View $this */

$this->title = 'Яблоки';
?>
<div class="site-index">

    <div class="jumbotron text-center bg-transparent">
        <h1 class="display-4"><?=  Html::a($this->title, 'apples/index', ['class' => 'btn btn-outline-primary']) ?></h1>
    </div>
</div>
