<?php

namespace App;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use App\Request;

class TwigExtensions extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('route', 'route'),
            new TwigFunction('isConnected', 'isConnected'),
            new TwigFunction('intervalleDate', 'intervalleDate')
        ];
    }
}

function route(string $name, array $param = null): string
{
    return Request::route($name, $param);
}

function isConnected()
{
    return isset($_SESSION['id']);
}

function intervalleDate($date = null) {
    if ($date === null) {
        $date = new \DateTime();
    }
    
    $dateActuelle = new \DateTime();
    $date = new \DateTime($date);
    $interval = $date->diff($dateActuelle);
    
    if ($interval->y > 0) {
        return $interval->format('%y ans, %m mois, %d jours');
    } elseif ($interval->m > 0) {
        return $interval->format('%m mois, %d jours');
    }
    return $interval->format('%d jours');
}
