<?php

namespace App\Services;

use Exception;

class OutfitGeneratorService
{
    const WIDTH = 192;

    const HEIGHT = 256;

    const BASE = [
        1 => ['Layers' => [1]]
    ];

    const HEAD = [
        1 => ['Layers' => [1]],
        2 => ['Layers' => [1]],
        3 => ['Layers' => [1]],
    ];

    const BODY = [
        1 => ['Layers' => [1]],
        2 => ['Layers' => [1]],
    ];

    const BACK = [
        1 => ['Layers' => [1]],
        2 => ['Layers' => [1, 2]],
        3 => ['Layers' => [1]],
    ];

    const HANDS = [
        1 => ['Layers' => [1, 3]]
    ];


    private array $images = [];

    private array $lookType = [];


    public function generate($params)
    {
        try {
            $this->setupLookType($params);
        } catch (Exception $e) {
            die('Caught exception: ' . $e->getMessage());
        }

        return $this->generateOutfit();
    }


    /**
     * @param $params
     * @throws Exception
     */
    private function setupLookType($params)
    {
        if (!is_string($params)) {
            throw new Exception('Wrong looktype!');
        }

        $params = explode(':', $params);
        if (count($params) !== 9) {
            throw new Exception('Wrong looktype!');
        }

        $this->lookType = array(
            'base' => (int)$params[0],
            'head' => (int)$params[1],
            'body' => (int)$params[2],
            'back' => (int)$params[3],
            'hands' => (int)$params[4],
            'head_color' => '#' . $params[5],
            'primary_color' => '#' . $params[6],
            'secondary_color' => '#' . $params[7],
            'detail_color' => '#' . $params[8]
        );

        if (!isset(self::BASE[$this->lookType['base']])) {
            throw new Exception('Unrecognized base!');
        }
        if ($this->lookType['head'] != 0 && !isset(self::HEAD[$this->lookType['head']])) {
            throw new Exception('Unrecognized head!');
        }
        if ($this->lookType['body'] != 0 && !isset(self::BODY[$this->lookType['body']])) {
            throw new Exception('Unrecognized body!');
        }
        if ($this->lookType['back'] != 0 && !isset(self::BACK[$this->lookType['back']])) {
            throw new Exception('Unrecognized back!');
        }
        if ($this->lookType['hands'] != 0 && !isset(self::HANDS[$this->lookType['hands']])) {
            throw new Exception('Unrecognized hands!');
        }

        if (!$this->isHexColorValid($this->lookType['head_color'])) {
            throw new Exception('Unrecognized head color!');
        }
        if (!$this->isHexColorValid($this->lookType['primary_color'])) {
            throw new Exception('Unrecognized primary color!');
        }
        if (!$this->isHexColorValid($this->lookType['secondary_color'])) {
            throw new Exception('Unrecognized secondary color!');
        }
        if (!$this->isHexColorValid($this->lookType['detail_color'])) {
            throw new Exception('Unrecognized detail color!');
        }

    }

    private function getColorsToReplace(): array
    {
        $replace = [];

        $colors = [
            ['from' => [255, 0, 0], 'to' => $this->hexToRGB($this->lookType['head_color'])],
            ['from' => [0, 255, 0], 'to' => $this->hexToRGB($this->lookType['primary_color'])],
            ['from' => [0, 0, 255], 'to' => $this->hexToRGB($this->lookType['secondary_color'])],
            ['from' => [255, 255, 0], 'to' => $this->hexToRGB($this->lookType['detail_color'])],
        ];

        foreach ($colors as $color) {
            $col1 = (($color['from'][0] & 0xFF) << 16) + (($color['from'][1] & 0xFF) << 8) + ($color['from'][2] & 0xFF);
            $col2 = (($color['to'][0] & 0xFF) << 16) + (($color['to'][1] & 0xFF) << 8) + ($color['to'][2] & 0xFF);
            $replace[$col1] = $col2;
        }

        return $replace;
    }

    private function generateOutfit()
    {
        $outfit = $this->prepareImage('base', 'real', 1);
        $overlay = $this->prepareImage('base', 'overlay', 1);

        foreach ([1, 2, 3] as $layer) {
            if ($this->lookType['body'] > 0 && in_array($layer, self::BODY[$this->lookType['body']]['Layers'])) {
                imagecopy($outfit, $this->prepareImage('body', 'real', $layer), 0, 0, 0, 0, self::WIDTH, self::HEIGHT);
                imagecopy($overlay, $this->prepareImage('body', 'overlay', $layer), 0, 0, 0, 0, self::WIDTH, self::HEIGHT);
            }
            if ($this->lookType['hands'] > 0 && in_array($layer, self::HANDS[$this->lookType['hands']]['Layers'])) {
                imagecopy($outfit, $this->prepareImage('hands', 'real', $layer), 0, 0, 0, 0, self::WIDTH, self::HEIGHT);
                imagecopy($overlay, $this->prepareImage('hands', 'overlay', $layer), 0, 0, 0, 0, self::WIDTH, self::HEIGHT);
            }
            if ($this->lookType['back'] > 0 && in_array($layer, self::BACK[$this->lookType['back']]['Layers'])) {
                imagecopy($outfit, $this->prepareImage('back', 'real', $layer), 0, 0, 0, 0, self::WIDTH, self::HEIGHT);
                imagecopy($overlay, $this->prepareImage('back', 'overlay', $layer), 0, 0, 0, 0, self::WIDTH, self::HEIGHT);
            }
            if ($this->lookType['head'] > 0 && in_array($layer, self::HEAD[$this->lookType['head']]['Layers'])) {
                imagecopy($outfit, $this->prepareImage('head', 'real', $layer), 0, 0, 0, 0, self::WIDTH, self::HEIGHT);
                imagecopy($overlay, $this->prepareImage('head', 'overlay', $layer), 0, 0, 0, 0, self::WIDTH, self::HEIGHT);
            }
        }
        $this->replaceColours($overlay, $this->getColorsToReplace());

        $this->images['outfit'] = BlendingModeService::blend($outfit, $overlay, 'multiply');

        return $this->images['outfit'];
    }

    private function prepareImage($addon, $type, $layer)
    {
        $this->images[$addon . '_' . $type . '_' . $layer] = imagecreatefrompng(resource_path('outfit') . '/' . $addon . '/' . $this->lookType[$addon] . '/' . $layer . '_' . $type . '.png');
        imagealphablending($this->images[$addon . '_' . $type . '_' . $layer], true);
        imagesavealpha($this->images[$addon . '_' . $type . '_' . $layer], true);

        return $this->images[$addon . '_' . $type . '_' . $layer];
    }

    private function replaceColours($img, $replace): void
    {
        imagealphablending($img, false);
        $transparent = imagecolortransparent($img);

        if (!imageistruecolor($img)) {
            imagepalettetotruecolor($img);
        }
        for ($x = 0; $x < imagesx($img); $x++) {
            for ($y = 0; $y < imagesy($img); $y++) {
                if (imagecolorat($img, $x, $y) === imagecolorallocate($img, 0, 0, 0)) {
                    imagesetpixel($img, $x, $y, $transparent);
                }
                if (array_key_exists($color = imagecolorat($img, $x, $y), $replace)) {
                    imagesetpixel($img, $x, $y, $replace[$color]);
                }
            }
        }
        imagealphablending($img, true);
    }

    private function isHexColorValid($hex): bool
    {
        preg_match('/^#[0-9A-F]{6}$/i', $hex, $result);

        return count($result) === 1;
    }

    private function hexToRGB($hex): array
    {
        $hex = str_replace('#', '', $hex);
        $split = str_split($hex, 2);
        $r = hexdec($split[0]);
        $g = hexdec($split[1]);
        $b = hexdec($split[2]);

        return [$r, $g, $b];
    }

}
