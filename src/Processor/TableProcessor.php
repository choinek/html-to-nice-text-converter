<?php

namespace Choinek\HtmlToNiceText\Processor;

class TableProcessor implements ContentProcessorInterface
{
    private const TABLE_START_PLACEHOLDER = '_START_TABLE_8d95d165-c9d2-43ef-bb3a-17d461a445ef_';
    private const TABLE_END_PLACEHOLDER = '_END_TABLE_8d95d165-c9d2-43ef-bb3a-17d461a445ef_';
    private const TABLE_PLACEHOLDER = '_CONVERTED_TABLE_8d95d165-c9d2-43ef-bb3a-17d461a445ef_';

    /**
     * @var array<string,string>
     */
    private array $tables = [];

    public function __construct(
        private int $beforePriority = 100,
        private int $afterPriority = 100
    ) {}

    public function before(string $content): string
    {
        $content = $this->htmlTableToAscii($content);
        $content = $this->moveTablesToMemory($content);
        $content = $this->cleanTablePlaceholders($content);

        return $content;
    }

    public function moveTablesToMemory(string $content): string
    {
        $contentWithoutTables = preg_replace_callback(
            '/' . self::TABLE_START_PLACEHOLDER . '(.*?)' . self::TABLE_END_PLACEHOLDER . '/s',
            function ($matches) {
                $placeholder = self::TABLE_PLACEHOLDER . count($this->tables);
                $this->tables[$placeholder] = str_replace([
                    self::TABLE_END_PLACEHOLDER,
                    self::TABLE_START_PLACEHOLDER
                ], '', $matches[0]);
                return $placeholder;
            },
            $content
        );

        return $contentWithoutTables;
    }

    public function after(string $content): string
    {
        foreach ($this->tables as $placeholder => $asciiTable) {
            $content = str_replace($placeholder, $asciiTable, $content);
        }
        return $content;
    }

    public function cleanTablePlaceholders(string $content): string
    {
        $content = preg_replace(
            '/[ \t]*(' . preg_quote(self::TABLE_PLACEHOLDER, '/') . '[a-f0-9-]+)/m',
            "\n$1",
            $content
        );

        return $content;
    }

    public function htmlTableToAscii(string $html): string
    {
        return preg_replace_callback(
            '/<table.*?>(.*?)<\/table>/si',
            function ($matches) {
                preg_match_all('/<tr>(.*?)<\/tr>/si', $matches[1], $rows);

                $table = [];
                $columnWidths = [];
                $minimumWidth = strlen(self::TABLE_END_PLACEHOLDER);

                foreach ($rows[1] as $row) {
                    preg_match_all('/<t[dh].*?>(.*?)<\/t[dh]>/si', $row, $cols);
                    $rowData = array_map(fn($cell) => trim(strip_tags($cell)), $cols[1]);

                    foreach ($rowData as $index => $cell) {
                        $columnWidths[$index] = max($columnWidths[$index] ?? 0, strlen($cell));
                    }

                    $table[] = $rowData;
                }

                $tableWidth = array_sum($columnWidths) + (3 * count($columnWidths)) + 1;
                if ($tableWidth < $minimumWidth) {
                    $lastColIndex = count($columnWidths) - 1;
                    $extraWidth = $minimumWidth - $tableWidth;
                    $columnWidths[$lastColIndex] += $extraWidth;
                    $tableWidth = $minimumWidth;
                }

                $output = [];
                $output[] = self::TABLE_START_PLACEHOLDER;
                $output[] = str_repeat('=', $tableWidth);

                foreach ($table as $rowIndex => $row) {
                    $formattedRow = '| ' . implode(' | ', array_map(
                            fn($cell, $index) => str_pad($cell, $columnWidths[$index]),
                            $row,
                            array_keys($columnWidths)
                        )) . ' |';

                    $output[] = $formattedRow;

                    if ($rowIndex === 0) {
                        $output[] = str_repeat('=', $tableWidth);
                    } else {
                        $output[] = str_repeat('-', $tableWidth);
                    }
                }

                $output[] = str_repeat('=', $tableWidth);
                $output[] = self::TABLE_END_PLACEHOLDER;

                return implode("\n", $output);
            },
            $html
        );
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
