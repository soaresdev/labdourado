<?php


namespace App\Support;

use Illuminate\Http\JsonResponse;

class Message
{
    private $text = 'Ação executada com sucesso!';
    private $type = 'success';
    private $data = [];
    private $errors = [];
    private $status = 200;

    public function getText()
    {
        return $this->text;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getData()
    {
        return $this->data;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setText(string $text)
    {
        $this->text = $text;
        return $this;
    }

    public function setType(string $type)
    {
        $this->type = $type;
        return $this;
    }

    public function setData(array $data)
    {
        $this->data = $data;
        return $this;
    }

    public function setErrors(array $errors)
    {
        $this->errors = $errors;
        return $this;
    }

    public function setStatus(int $status)
    {
        $this->status = $status;
        return $this;
    }

    public function success(?string $message = 'Ação executada com sucesso!'): Message
    {
        $this->type = 'success';
        $this->text = $message;
        $this->status = 200;
        return $this;
    }

    public function error(?string $message = 'Não foi possível executar essa ação no momento!'): Message
    {
        $this->type = 'error';
        $this->text = $message;
        $this->status = 400;
        return $this;
    }

    public function info(?string $message = 'Ação executada com sucesso!'): Message
    {
        $this->type = 'info';
        $this->text = $message;
        $this->status = 202;
        return $this;
    }

    public function getResponse(): JsonResponse
    {
        return response()->json([
            'type' => $this->getType(),
            'message' => $this->getText(),
            'data' => $this->getData(),
            'errors' => $this->getErrors(),
            'status' => $this->getStatus()
        ], $this->getStatus());
    }
}
