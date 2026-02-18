<?php
/**
 * Завдання 4: Латинська літера — голосна чи приголосна (switch)
 *
 * Символ для перевірки: '''
 * Категорії: голосна / приголосна / спеціальний символ (ь, ')
 * Очікуваний результат: "спеціальний символ"
 */
require_once __DIR__ . '/layout.php';

function isVowelOrConsonant(string $symbol): string
{
    switch (strtolower($symbol)) {
        case 'a':
        case 'e':
        case 'i':
        case 'o':
        case 'u':
            return "голосна";
        case 'ь':
        case "'":
            return "спеціальний символ";
        default:
            return "приголосна";
    }
}

// Вхідні дані (варіант 14)
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