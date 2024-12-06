<?php

namespace Choinek\HtmlToNiceText\Processor;

interface ContentProcessorInterface
{
    public function before(string $content): string;

    public function after(string $content): string;

    public function getBeforePriority(): int;

    public function getAfterPriority(): int;
}
