<?php

use yii\db\Migration;
use backend\models\Apple;

/**
 * Handles the creation of table `{{%apples}}`.
 */
class m251226_162314_create_apples_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->createTable('{{%apples}}', [
            'id' => $this->primaryKey(),
            'created_at' => $this->integer()->notNull()->comment('Появилось'),
            'age' => $this->integer(3)->unsigned()->notNull()->defaultValue(0)->comment('Возраст'),
            'size' => $this->integer(1)->unsigned()->notNull()->defaultValue(1)->comment('Размер'),
            'color' => $this->string(20)->notNull()->defaultValue(Apple::COLORS[0])->comment('Цвет'),
            'status' => $this->integer(1)->notNull()->defaultValue(Apple::STATUS_TREE)->comment('Статус'),
            'fall_at' => $this->integer()->comment('Упало'),
            'remain' => $this->integer(3)->unsigned()->notNull()->defaultValue(100)->comment('Осталось, %'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropTable('{{%apples}}');
    }
}
