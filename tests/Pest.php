<?php

declare(strict_types=1);

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Symfony\Component\Finder\SplFileInfo;
use Tests\TestCase;

pest()->extend(TestCase::class)->in('Feature', 'Architecture', 'Unit');

function relativeProjectPath(SplFileInfo $file): string
{
    return str_replace(base_path() . DIRECTORY_SEPARATOR, '', $file->getPathname());
}

function projectPhpFiles(): Collection
{
    return collect([
        ...File::allFiles(app_path()),
        ...File::allFiles(base_path('config')),
        ...File::allFiles(database_path()),
        ...File::allFiles(base_path('routes')),
        ...File::allFiles(base_path('tests')),
    ])->filter(fn (SplFileInfo $file): bool => $file->getExtension() === 'php'
        && ! str_ends_with($file->getFilename(), '.blade.php'));
}

function livewireComponentClasses(): array
{
    return classesInDirectory(app_path('Livewire'), 'App\\Livewire\\');
}

function modelClasses(): array
{
    return classesInDirectory(app_path('Models'), 'App\\Models\\');
}

function classesInDirectory(string $directory, string $namespace): array
{
    if ( ! is_dir($directory)) {
        return [];
    }

    $classes = [];

    foreach (File::allFiles($directory) as $file) {
        if ($file->getExtension() !== 'php') {
            continue;
        }

        $relative = mb_substr($file->getPathname(), mb_strlen($directory) + 1);
        $class = $namespace . str_replace(['/', '.php'], ['\\', ''], $relative);

        if (class_exists($class)) {
            $classes[] = $class;
        }
    }

    return $classes;
}
