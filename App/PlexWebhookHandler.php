<?php

namespace App;

/**
 * Class PlexWebhookHandler
 */
class PlexWebhookHandler
{

    private array $payload;
    private const EVENT_METHOD_BINDINGS = [
        'media.play' => 'play',
        'media.resume' => 'play',
        'media.pause' => 'stop',
        'media.stop' => 'stop',
    ];

    /**
     * PlexWebhookHandler constructor.
     *
     * @throws \Exception
     */
    public function __construct()
    {
        if (empty($_REQUEST['payload'])) {
            throw new \InvalidArgumentException('Missing payload');
        }

        $this->payload = json_decode($_REQUEST['payload'], true);

        if (isset(static::EVENT_METHOD_BINDINGS[$this->payload['event']])) {
            $this->{static::EVENT_METHOD_BINDINGS[$this->payload['event']]}();

            return;
        }

        throw new \Exception('Unhandled Plex event');
    }

    /**
     * @return \Hue
     */
    private function hue(): Hue
    {
        static $hue;

        if ($hue === null) {
            $hue = new Hue();
        }

        return $hue;
    }

    /**
     * @return string
     */
    protected function getDevice(): string
    {
        return $this->payload['Player']['uuid'];
    }

    /**
     * @return bool
     */
    protected function isMovie(): bool
    {
        return $this->payload['Metadata']['librarySectionType'] === 'movie';
    }

    /**
     * @return bool
     */
    protected function isShow(): bool
    {
        return $this->payload['Metadata']['librarySectionType'] === 'show';
    }

    /**
     * @return bool
     */
    protected function play(): bool
    {
        $dimmedBrightnessPercentage = ($this->isShow() ? 20 : 7);

        if ($this->getDevice() === device::OFFICE_PC) {
            return (bool)$this->hue()->setLightBrightness(device::OFFICE_DESK, $dimmedBrightnessPercentage);
        }

        if ($this->getDevice() === device::LOUNGE_TV) {
            return (bool)$this->hue()->setLightBrightness(device::LOUNGE_MAIN_LIGHT, 0) && $this->hue()->setLightBrightness(device::LOUNGE_LAMP, $dimmedBrightnessPercentage);
        }

        return false;
    }

    /**
     * @return bool
     */
    protected function stop(): bool
    {
        if ($this->getDevice() === device::OFFICE_PC) {
            return (bool)$this->hue()->setLightBrightness(device::OFFICE_DESK, 75);
        }

        if ($this->getDevice() === device::LOUNGE_TV) {
            return (bool)$this->hue()->setLightBrightness(device::LOUNGE_MAIN_LIGHT, 75) && $this->hue()->setLightBrightness(device::LOUNGE_LAMP, 75);
        }

        return false;
    }
}
