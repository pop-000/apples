<?php

use backend\models\Apple;
use yii\helpers\Html;

/**
 * @var Apple $model
 */

$session = Yii::$app->session;
$now = $session->get('now') ?? time(); 
$size = Apple::APPLE_START_SIZE + $model->size * Apple::APPLE_SIZE_INCREMENT;
$js = <<<JS
    $(".apple-menu-close").on('click', function(e){
        let me = $(e.target)
        let container = me.closest(".apple-menu-container")
        container.slideUp("slow")
    })
    $(".apple").on('click', function(){
        let me = $(this)
        let menu = me.next(".apple-menu-container")
        menu.slideDown("slow")
    })
    $(".eat").on('click', function(){
        let me = $(this)
        let appleId = me.data('appleId')
        if (me.data('status') == 2) {
            $.ajax({
                method: "POST",
                url: '/apples/eat',
                dataType: 'html',
                data: {
                    id: appleId
                },
                success: function(response) {
                    // $('#appleHref' + appleId).attr("xlink:href", "#apple-" + response)
                    me.closest(".apple-container").html(response)
                }
            })
        } else {
            let msg = ''
            if (me.data('status') == 3) {
                msg = "Яблоко сгнило. Поесть не получится..."
            } else {
                msg = "Яблоко на дереве. Надо ждать, пока упадет..."
            }
            $('#alert').text(msg)
            $('#alert').slideDown("slow")
            setTimeout(function(){
                $('#alert').slideUp()
            }, 3000)
        }
    })
JS;
$this->registerJs($js);
?>
<div class="apple-container">
    <?php
    echo Html::beginTag("div", [
        'class' => 'apple',
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
    // $content = '<use xlink:href="#apple-' . $model->remain . '" id="appleHref' . $model->id . '">';
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
                <li>Появилось: <?= date("H:i d.m.Y", $model->created_at) ?></li>
                <li>Возраст: <?= $model->age ?> дней.</li>
                <li>Размер: <?= $model->size ?>.</li>
                <li>Цвет: <?= $model->color ?>.</li>
                <li>Статус: <?= Apple::STATUSES[$model->status] . " [" . $model->status . "]" ?>.</li>
                <?php if (in_array($model->status, [Apple::STATUS_GROUND, Apple::STATUS_BAD])): ?>
                    <li>Упало: <?= date("H:i d.m.Y", $model->fall_at) ?></li>
                    <li>Часов на земле: <?= $model->getOnGroundHours($now) ?></li>
                <?php endif; ?>    
            </ul>
            <div class="menu-actions">
                <?= Html::button('Откусить', [
                    'class' => 'eat btn btn-outline-success',
                    'data' => [
                        'appleId' => $model->id,
                        'status' => $model->status
                    ],
                ]); ?>
            </div>
            <?= Html::button("&times;", ['class' => 'apple-menu-close']); ?>
        </div>
    </div>
</div>