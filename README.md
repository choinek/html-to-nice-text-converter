
# Content Processor Pipeline

A flexible and extensible library for processing and formatting content using a modular pipeline of processors.

## Features

- Modular processing with customizable priorities for `before` and `after` stages.
- Default processors for handling tables, trimming content, and cleaning text.
- A `FunctionProcessor` for custom logic via closures.
- Retains content structure while removing unnecessary spaces and newlines.

---

## Installation

1. Add the library to your project (assuming it's published to Packagist):

   ```bash
   composer require choinek/html-to-nice-text-converter
   ```

2. Ensure the `autoload` is properly loaded in your PHP script:

   ```php
   require 'vendor/autoload.php';
   ```

---

## How It Works

The pipeline consists of multiple processors, each responsible for a specific transformation. Each processor defines a `before` and `after` method, as well as priorities for both stages.

### Default Processors

#### `TableProcessor`
- **Purpose**: Converts HTML tables into ASCII tables.
- **Priority**:
   - `before`: `100` (runs first).
   - `after`: `300` (runs first in the after stage).

#### `DefaultProcessor`
- **Purpose**: Cleans content by replacing tags with newlines, stripping HTML, and collapsing excessive newlines.
- **Priority**:
   - `before`: `200` (runs after `TableProcessor`).
   - `after`: `400` (runs after `TableProcessor`).

#### `FunctionProcessor`
- **Purpose**: Allows custom processing logic via closures.
- **Priority**: Defined by the user when creating the processor.

---

## Example Usage

Check out the `example.php` script for a demonstration of the library in action.

```php example.php```

---

## Adding Custom Processors

To add your own processor, implement the `ContentProcessorInterface`:

```php
use Choinek\HtmlToNiceTextConverter\Processor\ContentProcessorInterface;

class CustomProcessor implements ContentProcessorInterface
{
    public function __construct(
        private int $beforePriority = 150,
        private int $afterPriority = 450
    ) {}

    public function before(string $content): string
    {
        // Custom before logic
        return $content;
    }

    public function after(string $content): string
    {
        // Custom after logic
        return $content;
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
```

Add it to the pipeline as follows:

```php
$formatter->addProcessor(new CustomProcessor(150, 450));
```

---

## License

MIT License
