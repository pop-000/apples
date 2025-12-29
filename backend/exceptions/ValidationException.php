<?php

namespace backend\exceptions;

class ValidationException extends \yii\base\UserException
{
    public $errors = [];
    
    public function __construct($errors, $message = null, $code = 0, \Exception $previous = null)
    {
        $this->errors = $errors;
        parent::__construct($message ?? 'Проверка провалилась', $code, $previous);
    }
    
    public function getName()
    {
        return 'Ошибка в данных';
    }
}