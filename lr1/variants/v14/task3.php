<?php
/**
 * Завдання 2: Конвертер валют (USD → UAH)
 *
 * 800 доларів → гривні, курс 42.15, комісія 3%
 */
require_once __DIR__ . '/layout.php';

function convertUsdToUah(float $USD, float $UAH): float
{
    return round($USD * $UAH, 2);
}

function applyCommission(float $amount, float $commissionPercent): float
{
    return round($amount * (1 - $commissionPercent / 100), 2);
}

// Вхідні дані (варіант 14)
$USD = 800;
$UAH = 42.15;
$commission = 3;

$eurBeforeCommission = convertUsdToUah($USD, $UAH);
$eurAfterCommission = applyCommission($eurBeforeCommission, $commission);

$content = '<div class="card">
    <h2>💶 Конвертер USD → UAH</h2>
    <p><strong>Курс:</strong> 1 USD = ' . $UAH . ' UAH</p>
    <p><strong>Комісія банку:</strong> ' . $commission . '%</p>
    <div class="result">' . $USD . ' USD = ' . $eurBeforeCommission . ' UAH</div>
    <div class="result mt-10 result-commission">Після комісії ' . $commission . '% — <strong>' . $eurAfterCommission . '</strong> UAH</div>
    <p class="info">convertUsdToUah(' . $USD . ', ' . $UAH . ') = ' . $eurBeforeCommission . '</p>
    <p class="info">applyCommission(' . $eurBeforeCommission . ', ' . $commission . ') = ' . $eurAfterCommission . '</p>
</div>';

renderVariantLayout($content, 'Завдання 2', 'task3-body');
