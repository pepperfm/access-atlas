<?php

declare(strict_types=1);

test('only uses Inertia Link where Inertia-specific behavior is required', function (): void {
    $allowedFiles = [];

    $linkImportPattern = "/import\\s*\\{[^}]*\\bLink\\b[^}]*\\}\\s*from\\s*'@inertiajs\\/vue3';/";

    $filesUsingInertiaLink = collect(iterator_to_array(
        new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(resource_path('js')),
        ),
    ))
        ->filter(fn(mixed $file): bool => $file instanceof \SplFileInfo)
        ->filter(fn(\SplFileInfo $file): bool => $file->isFile())
        ->map(fn(\SplFileInfo $file): string => $file->getPathname())
        ->filter(fn(string $path): bool => str_ends_with($path, '.vue'))
        ->filter(function (string $path) use ($linkImportPattern): bool {
            $contents = file_get_contents($path);

            return is_string($contents) && preg_match($linkImportPattern, $contents) === 1;
        })
        ->sort()
        ->values()
        ->all();

    expect($filesUsingInertiaLink)->toBe($allowedFiles);
});
