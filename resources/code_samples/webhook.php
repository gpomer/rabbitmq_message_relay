/**
* Connect to message router directly via webhook
*
* @param string $webhookpath url path for webhook
* @param array $payload data to pass onto message consumer
* @return string
*/
private function webhookRelay($webhookpath, $payload)
{
    $messagerouterUrl = env('MESSAGEROUTER_URL', null);

    if (empty($messagerouterUrl)) {
        $messagerouterUrl = env('APP_URL', 'https://messagerouter.ruckify.localhost');
    }

    $url = $messagerouterUrl."/$webhookpath";
    
    $handler = curl_init();
    curl_setopt($handler, CURLOPT_URL, $url);
    curl_setopt($handler, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($handler, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($handler, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($handler, CURLOPT_USERAGENT, 'Ruckify Message Router/1.0 (Ruckify Message Router)');
    curl_setopt($handler, CURLOPT_POST, true);
    curl_setopt($handler, CURLOPT_POSTFIELDS, json_encode($payload));
    
    $response = curl_exec($handler);

    return $response;
}