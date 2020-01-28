<?php

/**
 * Class naturalLighting
 *
 * Use to check the real-world lighting for a given location
 */
class NaturalLighting
{

    private const LOCATION = [
        'lat' => 51.034789,
        'lng' => -3.051500,
    ];

    /**
     * @return array
     */
    protected function getSunriseSunsetTimestamps()
    {
        $results = json_decode(file_get_contents('https://api.sunrise-sunset.org/json?' . http_build_query(static::LOCATION)), true)['results'];

        return [
            'sunrise' => strtotime($results['sunrise']),
            'sunset' => strtotime($results['sunset']),
        ];
    }

    /**
     * @return bool
     */
    public function isDark()
    {
        $timestamps = $this->getSunriseSunsetTimestamps();

        if (date('A') === 'AM') {
            return (time() < $timestamps['sunrise']);
        }

        return (time() > $timestamps['sunset']);
    }
}