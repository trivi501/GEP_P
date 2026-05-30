<?php

namespace App\Notifications;

class WhatsAppMessage
{
    public string $content;

    public ?string $templateName = null;

    public array $templateParameters = [];

    public static function create(): static
    {
        return new static;
    }

    public function content(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function template(string $name, array $parameters = []): static
    {
        $this->templateName = $name;
        $this->templateParameters = $parameters;

        return $this;
    }
}
