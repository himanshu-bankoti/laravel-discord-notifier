<?php

namespace Mountrix\DiscordNotification;

use Exception;
use Illuminate\Support\Facades\Facade;
use Psr\Http\Message\ResponseInterface;
use Mountrix\DiscordNotification\DiscordNotificationHandler;

class DiscordNotifier extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return DiscordNotifier::class;
    }
    public DiscordNotificationHandler $discordNotificationHandler;
    public function __construct(string $webhookUrl)
    {
        $this->discordNotificationHandler = new DiscordNotificationHandler($webhookUrl);
    }
    public function sendFailAlert(
        string $message,
        bool $isFailedNotification,
        string $alertMessage = null,
        array $alertData = null
    ): ResponseInterface {
        try {
            $finalData = [];

            if (!empty($alertData)) {
                $formattedDataArray = $this->formatMessage($alertData);

                $finalData = [
                    "title" => empty($alertMessage) ? "Error" : $alertMessage,
                    "color" => $this->pickEmbeddedColor($isFailedNotification ? 400 : 200),
                    "fields" => $formattedDataArray,
                ];
            }

            return $this->discordNotificationHandler->sendDiscordNotification(
                $message,
                $finalData
            );
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function formatMessage(array $errorData)
    {
        $fields = [];
        foreach ($errorData as $key => $value) {
            /**
             * Skipping alert message so that it doesn't come again in body of response.
             */
            if ($key == "alert_message") {
                continue;
            }

            if (is_array($value)) {
                $value = implode("\n", array_map(fn($k, $v) => "**$k:** $v", array_keys($value), $value));
            }

            $fields[] = [
                "name"   => ":arrow_right: " . ucfirst(str_replace("_", " ", $key)),
                "value"  => $value,
                "inline" => false,
            ];
        }

        return $fields;
    }

    public function pickEmbeddedColor(int $recordLevel)
    {
        return match ($recordLevel) {
            400 => 15158332,
            200 => 306700,
            default => 8421504,
        };
    }
}
