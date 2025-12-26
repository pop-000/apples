<?php

namespace backend\models;

use Yii;
use yii\db\Expression;

use function PHPUnit\Framework\throwException;

/**
 * This is the model class for table "apples".
 *
 * @property int $id
 * @property string $color Цвет
 * @property int $created_at Появилось
 * @property int|null $fall_at Упало
 * @property int $status Статус
 * @property int|null $remain Осталось, %
 * @property int $size Размер
 * @property bool $isBad Гнилое
 */
class Apple extends \yii\db\ActiveRecord
{
    const COLORS = ['red', 'darkred', 'lightred', 'tomato', 'yellow', 'yellowgreen', 'green', 'darkgreen', 'lightgreen'];

    const STATUS_TREE = 1;
    const STATUS_GROUND = 2;
    const STATUSES = [
        self::STATUS_TREE => 'На дереве', 
        self::STATUS_GROUND => 'На земле', 
    ];
    
    const DAYS_RANGE = 15; // начальная дата для создания яблока, дней назад
    const DAYS_READY = 10; // дней для созревания яблока
    const HOURS_EATABLE = 5; // часов годности
    const COUNT_APPLES_MIN = 5; // минимально кол-во яблок
    const COUNT_APPLES_MAX = 20; // максимальное кол-во яблок

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'apples';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fall_at'], 'default', 'value' => null],
            [['isBad', 'remain'], 'default', 'value' => false],
            [['color', 'created_at', 'status'], 'required'],
            [['created_at', 'fall_at', 'status', 'remain', 'isBad'], 'integer'],
            ['size', 'in', range(1, self::DAYS_READY)],
            [['isBad'], 'boolean'],
            [['color'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'color' => 'Цвет',
            'created_at' => 'Появилось',
            'fall_at' => 'Упало',
            'status' => 'Статус',
            'remain' => 'Осталось, %',
            'size' => 'Размер',
            'isBad' => 'Гнилое',
        ];
    }

    /**
     * Создает яблоко
     * @return null|common\models\Apple
     */
    public static function create() {
        $model = new self;
        $model->color = self::COLORS[rand(0, count(self::COLORS) - 1)] ?? null;
        $dateTo = time();
        $dateFrom = $dateTo - self::DAYS_RANGE * 60 * 60 * 24;
        $model->created_at = rand($dateFrom, $dateTo);
        $model->status = self::STATUS_GROUND; 
        $model->remain = 0; 
        if ($model->save()) {
            return $model;
        }
        return null;
    }

    /**
     * Определяет, какие яблоки лежат и испортились. Изменяет свойство isBad яблока.
     * @return void
     */
    public static function checkIsBad() {
        $applesOnGround = self::find()
            ->where(['not', ['fall_at' => null]])
            ->andWhere(['isBad' => false])
            ->all();

        foreach ($applesOnGround as $apple) {
            $timeOnGround = $apple->fall_at + self::HOURS_EATABLE * 60 * 60;
            if ($timeOnGround > time()) {
                $apple->isBad = true;
                $apple->save();
            }
        }    
    }

    /**
     * Определяет, какие яблоки созрели и упали. Изменяет свойства status и fall_at у яблока.
     * @return void
     */
    public static function checkReady() {
        $timeReady = self::DAYS_READY * 60 * 60 * 24;
        $condition = new Expression(time() . ' - created_at > ' . $timeReady . ' AND status = ' . self::STATUS_TREE);
        return self::updateAll($condition, [
            'status' => self::STATUS_GROUND,
            'fall_at' => time()
        ]);
    }

    /**
     * Определяет, как выросло яблоко. Изменяет свойство size у яблока.
     * @return void
     */
    public static function checkSize() {
        $apples = self::find()
            ->where(['status' => self::STATUS_TREE])
            ->all();

        foreach ($apples as $apple) {
            $daysLeft = (time() - $apple->created_at) / 60 * 60 * 24;
            $apple->size = round($daysLeft, 0);
            $apple->save();
        }
    }

    /**
     * Поедание яблока.
     * @param int $id // id яблока
     * @param int $eaten // съедено
     * @return null|int // сколько осталось, или null, если съели
     */
    public function eat($eaten) {
        $this->remain -= $eaten;
        if ($this->remain < 1) {
            $this->delete();
            return null;
        }
        $this->save();
        return $this->remain;
    }

    /**
     * Выращивание яблок.
     * @return void
     */
    public static function cultivate() {
        $count = rand(self::COUNT_APPLES_MIN, self::COUNT_APPLES_MAX);
        for ($i = $count; $i--; $i > 0) {
            self::create();
        }
    }

    /**
     * Проверяет, есть ли яблоки на дереве.
     * @return boolean
     */
    public static function isTreeEmpty() {
        return self::find()->exist() === false;
    }

}
