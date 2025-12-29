<?php

namespace backend\models;

use Yii;
use yii\db\Expression;
use backend\exceptions\ValidationException;

/**
 * This is the model class for table "apples".
 *
 * @property int $id
 * @property int $created_at Появилось
 * @property int $age Возраст
 * @property int $size Размер
 * @property string $color Цвет
 * @property int $status Статус
 * @property int $fall_at Упало
 * @property int $remain Осталось, %
 * 
 * @property int $onGroundHours Находится на земле, час
 * @property int $now Сейчас
 */
class Apple extends \yii\db\ActiveRecord
{
    public $now;
    public $onGroundHours;

    const COLORS = [
        'lightgreen',
        'green',
        'yellow',
        'orange',
        'orangered',
        'red',
    ];

    const BAD_COLOR = 'saddlebrown';

    const STATUS_TREE = 1;
    const STATUS_GROUND = 2;
    const STATUS_BAD = 3;
    const STATUSES = [
        self::STATUS_TREE => 'На дереве', 
        self::STATUS_GROUND => 'На земле', 
        self::STATUS_BAD => 'Гнилое', 
    ];
    
    const DAYS_BEFORE = 7; // начальная дата для создания яблока, дней назад
    const DAYS_READY = 5; // дней для созревания яблока
    const HOURS_EATABLE = 5; // часов годности
    const COUNT_APPLES_MIN = 12; // минимальное кол-во яблок
    const COUNT_APPLES_MAX = 20; // максимальное кол-во яблок

    const APPLE_START_SIZE = 25; // начальный размер яблока в px
    const APPLE_SIZE_INCREMENT = 3; // шаг роста в px
    
    const HOURS_INCREMENT = 3; // шаг времени, час
    const DAY_INCREMENT = 1; // шаг времени, дней

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
            ['age', 'default', 'value' => 0],
            ['size', 'default', 'value' => 1],
            ['color', 'default', 'value' => self::COLORS[0]],
            ['status', 'default', 'value' => self::STATUS_TREE],
            ['remain', 'default', 'value' => 100],
            [['created_at', 'age', 'size', 'color', 'status', 'remain', 'fall_at'], 'required'],
            [['created_at', 'fall_at'], 'integer'],
            [['status'], 'in', 'range' => array_keys(self::STATUSES)],
            [['remain'], 'in', 'range' => range(0, 100)],
            [['size'], 'in', 'range' => range(1, 6)],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'created_at' => 'Появилось',
            'age' => 'Возраст',
            'size' => 'Размер',
            'color' => 'Цвет',
            'status' => 'Статус',
            'fall_at' => 'Упало',
            'remain' => 'Осталось, %',
            'onGroundHours' => 'Находится на земле, час',
        ];
    }

    /**
     * Заполняет дерево яблоками.
     * @return void
     */
    public static function fillTree() {
        if (self::isTreeEmpty()) {
            $count = rand(self::COUNT_APPLES_MIN, self::COUNT_APPLES_MAX);
            for ($i = $count; $i--; $i > 0) {
                self::create();
            }
            // если на земле нет съедобных яблок, перезапускаем
            $isEatableExists = self::find()
                ->where(['status' => self::STATUS_GROUND])
                ->exists();
            if (!$isEatableExists) {
                self::truncate();
                self::fillTree();
            }
        }
    }

    /**
     * Определяет изменения у яблок на переданный момент времени.
     * @param int $now
     * @return void
     */
    public static function checkOnTime($now) {
        $apples = self::find()->all();
        foreach ($apples as $apple) {
            $apple->setAge($now);
            $apple->save();
        }
    }     

    /**
     * Создает яблоко
     * @return common\models\Apple
     * @throws ValidateException
     */
    public static function create() {
        $model = new self;
        $dateFrom = time() - self::DAYS_BEFORE * 60 * 60 * 24;
        $model->created_at = rand($dateFrom, time() - 5);
        $model->fall_at = $model->created_at + (self::DAYS_READY * 60 * 60 * 24);
        $model->setAge(time());
        $model->remain = 100; 
        if ($model->save()) {
            return $model;
        }
        throw new ValidationException($model->errors);
    }

    /**
     * Устанавливает атрибуты, зависимые от возраста яблока: статус, размер, цвет.
     * @param int $now
     * @return void
     */
    public function setAge($now) {
        if ($now <= $this->created_at) {
            return;
        }
        $this->age = ceil(($now - $this->created_at) / (60 * 60 * 24));
        $this->size = $this->age > 6 ? 6 : $this->age;
        
        if ($this->fall_at < $now) {
            $isBad = $this->getOnGroundHours($now) > self::HOURS_EATABLE;
            $this->status = $isBad ? self::STATUS_BAD : self::STATUS_GROUND;
        } else {
            $this->status = self::STATUS_TREE;
        }
        if ($this->status === Apple::STATUS_BAD) {
            $this->color = self::BAD_COLOR;
        } else {
            $this->color = self::COLORS[$this->age] ?? self::COLORS[5];
        }
    }

    /**
     * Сколько часов яблоко на земле. Устанавливает значение поля onGroundHours и возвращает значение.
     * @return int
     */
    public function getOnGroundHours($now) {
        $timeDiff = $now - $this->fall_at;
        $this->onGroundHours = floor($timeDiff / (60 * 60));
        return $this->onGroundHours;
    }

    /**
     * Поедание яблока.
     * @param int $id // id яблока
     * @param int $eaten // съедено, %
     * @return null|int // сколько осталось, или null, если съели
     */
    public function eat($eaten = 20) {
        $this->remain -= $eaten;
        $this->save();
        return $this->remain;
    }

    /**
     * Проверяет, есть ли яблоки на дереве.
     * @return boolean
     */
    public static function isTreeEmpty() {
        return self::find()->exists() === false;
    }

    /**
     * Очищает таблицу.
     * @return void
     */
    public static function truncate() {
        Yii::$app->db->createCommand()->truncateTable(self::tableName())->execute();
    }
}
