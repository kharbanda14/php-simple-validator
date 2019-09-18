<?php

class Validate
{
    private $schema = [];
    private $errors = [];
    private $source = [];
    private $formData = [];

    private $rules = [];
    private $messages = [];

    public function __construct($schema = [])
    {
        $this->schema = $schema;
        $this->source = $_POST;

        $this->rules = $this->getRules();
        $this->messages = $this->getMessages();
    }


    public function run()
    {
        $data = $this->source;

        if (empty($data)) {
            return false;
        }

        $rulesList = $this->rules;
        $messages = $this->messages;

        foreach ($this->schema as $key => $rules) {
            if (isset($data[$key])) {
                $rulesArray = explode('|', $rules);
                foreach ($rulesArray as $rule) {
                    if (!preg_match($rulesList[$rule], $data[$key])) {
                        $this->errors[$key] = sprintf($messages[$rule], $key);
                    }
                }
            }

            $this->formData[$key] = isset($data[$key]) ? $this->sanitize($data[$key]) : '';
        }

        return (count($this->errors) > 0) ? false : true;
    }

    public function getData()
    {
        return $this->formData;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function getRules()
    {
        return [
            'required' => '/.+/',
            'nullable' => '/.*/',
            'alphanum' => '/^[a-zA-Z0-9 ]+$/',
            'number' => '/^[0-9]+$/',
            'email' => '/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix',
        ];
    }

    public function addRule($field, $regex, $message = '')
    {
        if (!isset($this->rules[$field])) {
            $this->setRule($field, $regex, $message);
        }
    }

    public function setRule($field, $regex, $message = '')
    {
        $this->rules[$field] = $regex;
        $this->messages[$field] = $message;
    }

    public function getMessages()
    {
        return [
            'required' => '%s is required',
            'alphanum' => '%s can only have characters',
            'number' => '%s should be a valid number',
            'email' => '%s should be a valid email',
        ];
    }

    public function setMessage($field, $message)
    {
        if (isset($this->messages[$field])) {
            $this->messages[$field] = $message;
        }
    }

    private function sanitize($str)
    {
        $data = trim($str);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }
}
