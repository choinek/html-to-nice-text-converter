<?php


namespace Choinek\HtmlToNiceText\Processor;

class DefaultProcessor implements ContentProcessorInterface
{
    public function __construct(
        private int $beforePriority = 200,
        private int $afterPriority = 200
    ) {
    }

    public function before(string $content): string
    {
        // Replace <br>, <p>, <div>, and <li> tags with newlines
        $content = preg_replace('/<(br|p|div|li)[^>]*>/i', PHP_EOL, $content);

        // Strip all remaining HTML tags
        $content = strip_tags($content);

        // remove leading/trailing spaces and tabs from each line
        $content = preg_replace('/^[ \t]+/m', '', $content);

        // Collapse sequences of 3 or more newlines into exactly 2 newlines
        $content = preg_replace('/(\r?\n){2,}/', PHP_EOL . PHP_EOL, $content);

        return trim($content);
    }

    public function after(string $content): string
    {
        return trim($content);
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
