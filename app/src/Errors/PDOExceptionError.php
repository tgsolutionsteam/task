<?php

namespace App\Errors;

use PDOException;

class PDOExceptionError implements ExceptionInterface
{
    private $exception;
    private $errors;
    private $message;
    private $variables = [];

    public function __construct(PDOException $exception)
    {
        $this->exception = $exception;
        $classnameMessages = '\\App\Errors\\Messages\\' . get_class($exception) . 'Messages';
        $this->errors = new $classnameMessages();
        $this->message = $exception->getMessage();
    }

    /**
     * {@inheritdoc}
     */
    public function getErrorMessage(): string
    {
        $codeNum = (int)$this->exception->getCode();
        $this->setReplaceableVariables($codeNum);

        if (array_key_exists($codeNum, $this->errors->getMessages())) {
            $this->message = $this->errors->getMessages()[$codeNum];
            if (is_array($this->message) && !is_null($this->exception->errorInfo)) {
                if (isset($this->message[$this->exception->errorInfo[1]])) {
                    $this->message = $this->message[$this->exception->errorInfo[1]];
                }
                if (is_array($this->message)) {
                    $this->message = $this->exception->getMessage();
                }
            }
        }

        $this->replaceVariables();
        return $this->message;
    }

    private function setReplaceableVariables(int $codeNum): void
    {
        $codesToFind = [23000, 1000];

        if (in_array($codeNum, $codesToFind) && !is_null($this->exception->errorInfo)) {
            $match = null;
            if (preg_match("/'(.*?)'/", $this->exception->getMessage(), $match)) {
                $this->variables['value'] = "'{$match[1]}'";
            }
        }
    }

    private function replaceVariables(): void
    {
        foreach ($this->variables as $key => $value) {
            $placeholder = sprintf('{{%s}}', $key);
            $this->message = str_replace($placeholder, $value, $this->message);
        }
    }
}
