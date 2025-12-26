<?php

use backend\models\Apple;
use yii\helpers\Html;

/**
 * @var Apple $model
 */

echo Html::tag("div", [
    'class' => 'apple',
    'style' => [
        'width' => $model->size * 8,
        'color' => $model->isBad ? 'brown' : $model->color,
    ]
]);
?>