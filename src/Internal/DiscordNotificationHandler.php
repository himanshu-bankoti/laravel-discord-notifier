<?php

namespace Mountrix\DiscordNotification\Internal;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
/**
 * @internal Handler
 */
class DiscordNotificationHandler
{
    protected string $webHookUrl;
    public function __construct(string $webHookUrl)
    {
        $this->webHookUrl = $webHookUrl;
    }
    public function sendDiscordNotification(
        string $message,
        ?array $data = null
    ): ResponseInterface {

        $client   = new Client();
        $postData = $this->preparePostData($data, $message);

        $response = $client->post($this->webHookUrl, $postData);

        return $response;
    }

    protected function preparePostData($data, $message)
    {
        $postData = [
            'json' => [
                'content' => $message,
            ],
        ];

        if (! empty($data)) {
            $postData['json']['embeds'] = [$data];
        }

        return $postData;
    }
}
