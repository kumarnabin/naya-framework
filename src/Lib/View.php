<?php

namespace Konnect\NayaFramework\Lib;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class View
{
    protected static string $viewPath = 'views';

    protected static Environment $twig;

    // Initialize Twig Environment
    public static function init(): void
    {
        $loader = new FilesystemLoader('../' . self::$viewPath);
        self::$twig = new Environment($loader, [
            'cache' => false, // Set to 'cache' folder for production
        ]);
    }

    // Render the Twig template view
    public static function render($viewName, $data = []): void
    {
        self::init();
        try {
            echo self::$twig->render($viewName . '.twig', $data);
        } catch (\Twig\Error\LoaderError|\Twig\Error\RuntimeError|\Twig\Error\SyntaxError $e) {
            echo "Error rendering view: " . $e->getMessage();
        }
    }
}

