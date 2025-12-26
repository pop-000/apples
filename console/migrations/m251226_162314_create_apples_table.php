<?php

use yii\db\Migration;

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
            'color' => $this->string(20)->notNull()->comment('Цвет'),
            'created_at' => $this->integer()->notNull()->comment('Появилось'),
            'fall_at' => $this->integer()->comment('Упало'),
            'status' => $this->integer(1)->notNull()->defaultValue(1)->comment('Статус'),
            'remain' => $this->integer(3)->unsigned()->notNull()->defaultValue(100)->comment('Осталось, %'),
            'size' => $this->integer(1)->unsigned()->notNull()->defaultValue(1)->comment('Размер'),
            'isBad' => $this->boolean()->notNull()->defaultValue(false)->comment('Гнилое'), 
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
