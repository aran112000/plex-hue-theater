<?php

/**
 * Class Hue
 */
class Hue
{

    const ONLY_LIGHT_WHEN_DARK = true;

    /**
     * @return bool
     */
    protected function enabled(): bool
    {
        static $enabled;

        if ($enabled === null) {
            $enabled = true;

            if (static::ONLY_LIGHT_WHEN_DARK && !(new naturalLighting)->isDark()) {
                $enabled = false;
            }
        }

        return $enabled;
    }

    /**
     * @param int $light
     * @param int $percentage
     *
     * @return array|null
     */
    public function setLightBrightness(int $light, int $percentage): ?array
    {
        return $this->apiCall('lights/' . $light . '/state', [
            'on' => ($percentage > 0),
            'bri' => ceil($percentage * 2.55),
        ], 'PUT');
    }

    /**
     * @return array
     */
    public function getAllLights(): array
    {
        return $this->apiCall('lights');
    }

    /**
     * @param string $endpoint
     * @param array  $payload
     * @param string $method
     *
     * @return array|null
     */
    private function apiCall(string $endpoint, array $payload = [], $method = 'GET'): ?array
    {
        if (!$this->enabled()) {
            return null;
        }

        $ch = curl_init($this->getApiEndpoint($endpoint));
        curl_setopt_array($ch, [
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => [
                'Content-type: application/json',
            ]
        ]);
        $response = json_decode(curl_exec($ch), true);
        curl_close($ch);

        return $response;
    }

    /**
     * @param string $endpoint
     *
     * @return string
     * @throws \Exception
     */
    private function getApiEndpoint(string $endpoint): string
    {
        $bridgeIp = Setting::get('hue', 'local_bridge_ip');
        if (stristr($bridgeIp, 'x')) {
            throw new Exception('Please ensure you specify your settings in settings.ini (copy settings.ini.example --> settings.ini and follow the instructions at the top of the file to generate your required settings)');
        }

        return 'https://' . $bridgeIp . '/api/' . Setting::get('hue', 'api_user') . '/' . trim($endpoint, ' /');
    }
}