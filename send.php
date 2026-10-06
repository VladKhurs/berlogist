<?php
header('Content-Type: application/json; charset=utf-8');

// Принимаем только POST-запросы
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Метод не поддерживается']);
    exit;
}

$rawInput = file_get_contents('php://input');
$data = json_decode($rawInput, true);

if (!$data) {
    echo json_encode(['success' => false, 'error' => 'Нет данных']);
    exit;
}

$name    = isset($data['Имя']) ? htmlspecialchars(trim($data['Имя'])) : '';
$phone   = isset($data['Телефон']) ? htmlspecialchars(trim($data['Телефон'])) : '';
$type    = isset($data['Кто заказывает']) ? htmlspecialchars(trim($data['Кто заказывает'])) : 'Физлицо';
$service = isset($data['Услуга']) ? htmlspecialchars(trim($data['Услуга'])) : 'Не указана';
$comment = isset($data['Комментарий']) ? htmlspecialchars(trim($data['Комментарий'])) : '—';

if (empty($name) || empty($phone)) {
    echo json_encode(['success' => false, 'error' => 'Заполните обязательные поля']);
    exit;
}

// ========================================================
// ВСТАВЬТЕ СЮДА ВАШИ ДАННЫЕ ИЗ ШАГОВ 1 И 2
// ========================================================
$botToken = '';
$chatId   = '-';
// ========================================================

// Красивое оформление сообщения для Telegram
$text  = "🚨 <b>НОВАЯ ЗАЯВКА С САЙТА!</b>\n\n";
$text .= "👤 <b>Имя:</b> {$name}\n";
$text .= "📞 <b>Телефон:</b> <code>{$phone}</code>\n";
$text .= "💼 <b>Кто заказывает:</b> {$type}\n";
$text .= "📦 <b>Услуга:</b> {$service}\n";
$text .= "💬 <b>Комментарий:</b> {$comment}\n\n";
$text .= "🕒 <i>" . date('d.m.Y H:i') . "</i>";

// Отправка запроса в Telegram API
$url = "https://api.telegram.org/bot{$botToken}/sendMessage";
$postData = [
    'chat_id'    => $chatId,
    'text'       => $text,
    'parse_mode' => 'HTML'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$resData = json_decode($response, true);

if ($httpCode === 200 && isset($resData['ok']) && $resData['ok'] === true) {
    echo json_encode(['success' => true]);
} else {
    $errDesc = isset($resData['description']) ? $resData['description'] : 'Ошибка отправки в Telegram';
    echo json_encode(['success' => false, 'error' => $errDesc]);
}
