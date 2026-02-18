<?php
/**
 * Завдання 4: Тип символу (switch)
 *
 * Символ для перевірки: '''
 * Категорії: голосна / приголосна / спеціальний символ (ь, ')
 * Очікуваний результат: "спеціальний символ"
 */
require_once __DIR__ . '/layout.php';

function getSymbolType(string $symbol): string
{
    switch (strtolower($symbol)) {
        case 'а':
        case 'е':
        case 'і':
        case 'о':
        case 'у':
        case 'и':
            return "голосна";
        case 'ь':
        case "'":
            return "спеціальний символ";
        default:
            return "приголосна";
    }
}

// Вхідні дані
$symbol = "'";

$result = getSymbolType($symbol);

$isVowel = $result === "голосна";
$isSpecial = $result === "спеціальний символ";

$color = $isVowel ? "#10b981" : ($isSpecial ? "#f59e0b" : "#8b5cf6");
$emoji = $isVowel ? "🔊" : ($isSpecial ? "⚠️" : "🔇");

$content = '<div class="card large">
    <div class="letter-display" style="color:' . $color . '">' . htmlspecialchars($symbol) . '</div>
    <div class="letter-emoji" style="color:' . $color . '">' . $emoji . '</div>
    <div class="letter-result">
        Символ <strong>\'' . htmlspecialchars($symbol) . '\'</strong> — 
        <span style="color:' . $color . '">' . $result . '</span>
    </div>
    <p class="info">getSymbolType(\'' . htmlspecialchars($symbol) . '\') = "' . $result . '"</p>
</div>';

renderVariantLayout($content, 'Завдання 4', 'task5-body');
