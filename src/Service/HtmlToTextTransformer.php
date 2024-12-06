<?php

namespace Choinek\HtmlToNiceText\Service;

use Choinek\HtmlToNiceText\Processor\ContentProcessorInterface;
use Choinek\HtmlToNiceText\Processor\DefaultProcessor;
use Choinek\HtmlToNiceText\Processor\TableProcessor;

class HtmlToTextTransformer
{
    /**
     * @var ContentProcessorInterface[]
     */
    private array $beforeProcessors = [];
    /**
     * @var ContentProcessorInterface[]
     */
    private array $afterProcessors = [];

    /**
     * @param array<ContentProcessorInterface>|null $processors
     */
    public function __construct(?array $processors = null)
    {
        if ($processors === null) {
            // Add default processors
            $this->addProcessor(new TableProcessor());
            $this->addProcessor(new DefaultProcessor());
        } else {
            foreach ($processors as $processor) {
                $this->addProcessor($processor);
            }
        }
    }

    public function addProcessor(ContentProcessorInterface $processor): void
    {
        // Add to beforeProcessors if priority is >= 0
        if ($processor->getBeforePriority() >= 0) {
            $this->beforeProcessors[] = $processor;
            usort($this->beforeProcessors, fn(
                ContentProcessorInterface $a,
                ContentProcessorInterface $b
            ) => $a->getBeforePriority() <=> $b->getBeforePriority());
        }

        // Add to afterProcessors if priority is >= 0
        if ($processor->getAfterPriority() >= 0) {
            $this->afterProcessors[] = $processor;
            usort($this->afterProcessors, fn(
                ContentProcessorInterface $a,
                ContentProcessorInterface $b
            ) => $a->getAfterPriority() <=> $b->getAfterPriority());
        }
    }

    public function formatEmailContent(string $html): string
    {
        foreach ($this->beforeProcessors as $processor) {
            $html = $processor->before($html);
        }

        foreach ($this->afterProcessors as $processor) {
            $html = $processor->after($html);
        }

        return $html;
    }
}
