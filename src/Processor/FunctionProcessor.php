<?php

namespace Choinek\HtmlToNiceText\Processor;

use Closure;

class FunctionProcessor implements ContentProcessorInterface
{
    public function __construct(
        private int $beforePriority,
        private int $afterPriority,
        private Closure $callbackBefore,
        private Closure $callbackAfter
    ) {}

    public function before(string $content): string
    {
        return ($this->callbackBefore)($content);
    }

    public function after(string $content): string
    {
        return ($this->callbackAfter)($content);
    }

    public function getBeforePriority(): int
    {
        return $this->beforePriority;
    }

    public function getAfterPriority(): int
    {
        return $this->afterPriority;
    }
}
