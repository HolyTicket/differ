<?php
namespace App\Validators;

use Connect;

class CustomValidator extends \Illuminate\Validation\Validator {

    private $error;
    public function validateConnection($attribute, $value, $parameters)
    {
        $connection = Connect::connect('test', $this->data);
        Connect::reset();
        $this->error = $connection;
        return $connection === true;
    }
    protected function replaceConnection($message, $attribute, $rule, $parameters)
    {
        return str_replace(':error', $this->error, $message);
    }

}