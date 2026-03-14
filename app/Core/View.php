<?php

declare(strict_types=1);

namespace App\Core;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

final class View
{
    private Environment $twig;

    public function __construct(string $viewsPath, string $cachePath, array $appConfig)
    {
        $loader = new FilesystemLoader($viewsPath);

        $this->twig = new Environment($loader, [
            'cache' => ($appConfig['env'] === 'production') ? $cachePath : false,
            'debug' => (bool) $appConfig['debug'],
            'autoescape' => 'html',
        ]);

        $this->twig->addGlobal('app', $appConfig);
    }

    public function render(string $template, array $data = []): void
    {
        echo $this->twig->render($template, $data);
    }

    public function addGlobal(string $name, mixed $value): void
    {
        $this->twig->addGlobal($name, $value);
    }
}
