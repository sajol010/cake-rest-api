<?php
$statusKey = $responseFormat['statusKey'] ?? 'status';
$resultKey = $responseFormat['resultKey'] ?? 'result';
$messageKey = $responseFormat['messageKey'] ?? 'message';
$defaultMessage = $responseFormat['defaultMessageText'] ?? 'Empty response!';

if (empty($response[$resultKey])) {
    $response[$resultKey] = [
        $messageKey => $defaultMessage
    ];
}
