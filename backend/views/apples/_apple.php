<?php

use backend\models\Apple;
use yii\helpers\Html;

/**
 * @var Apple $model
 */

$session = Yii::$app->session;
$now = $session->get('now') ?? time(); 
$size = Apple::APPLE_START_SIZE + $model->size * Apple::APPLE_SIZE_INCREMENT;
?>
<div class="apple-container">
    <?php
    echo Html::beginTag("div", [
        'class' => 'apple',
        'id' => 'apple-' . $model->id,
        'style' => [
            'width' => $size . 'px',
            'height' => $size . 'px',
        ],
        'data' => [
            'age' => $model->age,
            'size' => $model->size,
            'status' => $model->status,
            ]
    ]);
    $content = '<use xlink:href="#apple-' . $model->remain . '" >';
    echo Html::tag('svg', $content, [
        'viewbox' => "0 0 50 50",
        'xmlns' => "http://www.w3.org/2000/svg",
        'style' => [
            'fill' => $model->color,
            'stroke' => 'black',
            'stroke-width' => 1,
            'width' => "100%",
            'height' => "100%",
        ]
    ]);
    echo Html::endTag("div");

    $containerClass = 'apple-menu-container';
    $containerClass .= $model->status === Apple::STATUS_TREE ? ' tree' : ' ground';
    ?>
    <div class="<?=  $containerClass ?>">
        <div class="apple-menu">
            <ul class="menu-info">
                <li>ID: <?= $model->id ?></li>
                <li>Появилось: <?= date("H:i d.m.Y", $model->created_at) ?></li>
                <li>Возраст: <?= $model->age ?> дней.</li>
                <li>Размер: <?= $model->size ?>.</li>
                <li>Цвет: <?= $model->color ?>.</li>
                <li>Статус: <?= Apple::STATUSES[$model->status] . " [" . $model->status . "]" ?>.</li>
                <?php if (in_array($model->status, [Apple::STATUS_GROUND, Apple::STATUS_BAD])): ?>
                    <li>Упало: <?= date("H:i d.m.Y", $model->fall_at) ?></li>
                    <li>Часов на земле: <?= $model->getOnGroundHours($now) ?></li>
                <?php endif; ?>
                <?php if ($model->remain < 100): ?>
                    <li>Осталось: <?= $model->remain ?>%</li>
                <?php endif; ?>    
            </ul>
            <div class="menu-actions">
                <?= Html::button('Откусить', [
                    'class' => 'eat btn btn-outline-success',
                    'data' => [
                        'apple_id' => $model->id,
                        'status' => $model->status,
                        'remain' => $model->remain,
                    ],
                ]); ?>
            </div>
            <?= Html::button("&times;", ['class' => 'apple-menu-close']); ?>
        </div>
    </div>
</div>