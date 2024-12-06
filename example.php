<?php

require __DIR__ . '/vendor/autoload.php';

use Choinek\HtmlToNiceText\Processor\DefaultProcessor;
use Choinek\HtmlToNiceText\Service\HtmlToTextTransformer;
use Choinek\HtmlToNiceText\Processor\TableProcessor;
use Choinek\HtmlToNiceText\Processor\FunctionProcessor;

$html = '
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.5; color: #333;">
    <header style="background-color: #f4f4f4; padding: 20px; text-align: center;">
        <h1 style="margin: 0;">Thank You for Your Order!</h1>
        <p style="margin: 5px 0;">Order #12345</p>
    </header>

    <main style="padding: 20px;">
        <p>Dear <strong>John Doe</strong>,</p>
        <p>We are pleased to confirm your order. Below are the details of your purchase:</p>

        <h2>Order Summary</h2>
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
            <tr style="background-color: #f4f4f4;">
                <th style="text-align: left;">Product</th>
                <th style="text-align: left;">Quantity</th>
                <th style="text-align: left;">Price</th>
            </tr>
            <tr>
                <td>Wireless Mouse</td>
                <td>2</td>
                <td>$50</td>
            </tr>
            <tr>
                <td>Bluetooth Keyboard</td>
                <td>1</td>
                <td>$80</td>
            </tr>
            <tr>
                <td>USB-C Charger</td>
                <td>1</td>
                <td>$30</td>
            </tr>
        </table>

        <h2>Shipping Details</h2>
        <table border="1" cellpadding="5" cellspacing="0" style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
            <tr style="background-color: #f4f4f4;">
                <th style="text-align: left;">Address</th>
                <th style="text-align: left;">Estimated Delivery</th>
            </tr>
            <tr>
                <td>123 Main Street, Apartment 4B, New York, NY, 10001</td>
                <td>Dec 10, 2024</td>
            </tr>
        </table>

        <p>If you have any questions about your order, please don’t hesitate to contact us at <a href="mailto:support@ourstore.com">support@ourstore.com</a>.</p>
    </main>

    <footer style="background-color: #f4f4f4; padding: 10px; text-align: center;">
        <p style="margin: 5px 0;">&copy; 2024 Our Store. All rights reserved.</p>
    </footer>
</body>
</html>
';

$formatter = new HtmlToTextTransformer();

$formatter->addProcessor(new TableProcessor());
$formatter->addProcessor(new DefaultProcessor());
$formatter->addProcessor(new FunctionProcessor(
    1,
    1000,
    function (string $content): string {
        return "Function processor example before everything\n" . $content;
    },
    function (string $content): string {
        return $content . "\nGenerated by FunctionProcessor After everything.";
    }
));

$formattedContent = $formatter->formatEmailContent($html);

echo "Formatted Content:\n\n";
echo $formattedContent;
