<?php

class Validator
{
    private $email;
    private $termsAgreed;

    public $isOk = true;
    public $error;

    const FORBIDDEN_EMAILS = ['.co'];

    public function __construct($params)
    {
        $this->email = $params['email'];
        $this->termsAgreed = $params['termsAgreed'];
    }

    public function initValidation()
    {
        $this->validateEmail();
        $this->validateSecondary();
    }

    private function validateEmail()
    {
        $pattern = '/^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/';

        if ($this->email == null) {
            $this->errorFound('Email address is required');
            return;
        }

        if (!preg_match($pattern, $this->email)) {
            $this->errorFound('Please provide valid email address!');
            return;
        }

        $extension = substr($this->email, -3);

        if (in_array($extension, self::FORBIDDEN_EMAILS)) {
            $this->errorFound('We are not accepting subscriptions from Colombia emails.');
        }
    }

    private function validateSecondary()
    {
        if (!$this->termsAgreed) {
            $this->errorFound("You must accept the terms and conditions");
        }
    }

    private function errorFound($errorMessage)
    {
        $this->isOk = false;
        $this->error = $errorMessage;
    }
}