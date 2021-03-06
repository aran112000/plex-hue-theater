<?php

namespace App;

/**
 * Class naturalLighting
 *
 * Use to check the real-world lighting for a given location
 */
class NaturalLighting
{

    /**
     * @return array
     */
    protected function getSunriseSunsetTimestamps()
    {
        $location = [
            'lat' => Setting::get('location', 'lat'),
            'lng' => Setting::get('location', 'lng'),
        ];

        $results = json_decode(file_get_contents('https://api.sunrise-sunset.org/json?' . http_build_query($location)), true)['results'];

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
