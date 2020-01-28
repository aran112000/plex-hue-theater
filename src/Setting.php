<?php

/**
 * Class Setting
 */
class Setting
{

    /**
     * @param string $block
     * @param string $setting
     *
     * @return mixed
     * @throws \Exception
     */
    public static function get(string $block, string $setting)
    {
        static $settings;

        if ($settings === null) {
            $settingFile = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'settings.ini';

            if (!is_readable($settingFile)) {
                throw new Exception('Missing settings.ini file, rename settings.ini.example --> settings.ini and follow the instructions at the top of the file to generate your required settings');
            }

            $settings = parse_ini_file(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'settings.ini', true);
        }

        if (!empty($settings[$block][$setting])) {
            return $settings[$block][$setting];
        }

        throw new \Exception('Setting not defined in settings.ini: ' . $block . ':' . $setting);
    }
}