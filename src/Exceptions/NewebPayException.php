<?php

namespace Ycs77\NewebPay\Exceptions;

use RuntimeException;

class NewebPayException extends RuntimeException
{
    public function __construct(
        protected string $apiStatus,
        protected string $apiMessage,
        protected ?string $apiUrl = null,
        protected ?array $formData = null
    ) {
        parent::__construct(sprintf(
            'NewebPay API 回應錯誤 (Code: %s)：「%s」', $this->apiStatus, $this->apiMessage
        ));
    }

    /**
     * Get the exception's context information.
     *
     * @return array<string, mixed>
     */
    public function context(): array
    {
        $context = [];

        if ($this->apiUrl) {
            $context['url'] = $this->apiUrl;
        }

        if ($this->formData) {
            $context['formdata'] = $this->formData;
        }

        return $context;
    }

    public function getApiUrl(): string
    {
        return $this->apiUrl;
    }

    public function getFormData(): array
    {
        return $this->formData;
    }

    public function getApiStatus(): string
    {
        return $this->apiStatus;
    }

    public function getApiMessage(): string
    {
        return $this->apiMessage;
    }
}
