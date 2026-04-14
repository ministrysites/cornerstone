<?php

declare(strict_types=1);

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Modelable;
use Livewire\Attributes\Url;
use Livewire\Attributes\Validate;
use Livewire\Component;

arch('app code uses strict types')
    ->expect('App')
    ->toUseStrictTypes();

arch('controllers extend the base controller')
    ->expect('App\Http\Controllers')
    ->classes()
    ->toExtend(Controller::class)
    ->ignoring(Controller::class);

arch('livewire components extend the livewire base component')
    ->expect('App\Livewire')
    ->classes()
    ->toExtend(Component::class);

test('public livewire properties are explicitly bound, locked, or synced', function (): void {
    $allowed = [Locked::class, Validate::class, Url::class, Modelable::class];
    $inspected = 0;

    $files = collect(File::allFiles(app_path('Livewire')))
        ->filter(fn (SplFileInfo $file): bool => $file->getExtension() === 'php');

    $files->each(function (SplFileInfo $file) use ($allowed, &$inspected): void {
        $relative = relativeProjectPath($file);
        $class = 'App\\Livewire\\' . str_replace(
            ['/', '.php'],
            ['\\', ''],
            mb_substr($file->getPathname(), mb_strlen(app_path('Livewire')) + 1)
        );

        if ( ! class_exists($class)) {
            return;
        }

        $reflection = new ReflectionClass($class);

        if ( ! $reflection->isSubclassOf(Component::class)) {
            return;
        }

        foreach ($reflection->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            if ($property->getDeclaringClass()->getName() !== $class) {
                continue;
            }

            if ($property->isStatic() || $property->isReadOnly()) {
                continue;
            }

            $inspected++;

            $hasAllowed = collect($allowed)
                ->contains(fn (string $attr): bool => $property->getAttributes($attr) !== []);

            expect($hasAllowed)->toBeTrue(sprintf(
                'Public Livewire property [%s::$%s] in [%s] must be marked with #[Locked], #[Validate], #[Url], or #[Modelable].',
                $class,
                $property->getName(),
                $relative
            ));
        }
    });

    expect($inspected)->toBeGreaterThan(0, 'Livewire property rule did not reflect any public properties; the walk is a silent no-op.');
});

function relativeProjectPath(SplFileInfo $file): string
{
    return str_replace(base_path() . DIRECTORY_SEPARATOR, '', $file->getPathname());
}

function fileCallsAnyFunction(SplFileInfo $file, array $functions): bool
{
    $tokens = token_get_all(File::get($file->getRealPath()));
    $functions = array_map('strtolower', $functions);
    $skip = [T_WHITESPACE, T_COMMENT, T_DOC_COMMENT];
    $count = count($tokens);

    for ($i = 0; $i < $count; $i++) {
        $token = $tokens[$i];

        if ( ! is_array($token) || $token[0] !== T_STRING) {
            continue;
        }

        if ( ! in_array(mb_strtolower($token[1]), $functions, true)) {
            continue;
        }

        $prev = null;
        for ($j = $i - 1; $j >= 0; $j--) {
            if (is_array($tokens[$j]) && in_array($tokens[$j][0], $skip, true)) {
                continue;
            }

            $prev = $tokens[$j];
            break;
        }

        if (is_array($prev) && in_array($prev[0], [T_OBJECT_OPERATOR, T_NULLSAFE_OBJECT_OPERATOR, T_DOUBLE_COLON, T_FUNCTION, T_NS_SEPARATOR], true)) {
            continue;
        }

        $next = null;
        for ($j = $i + 1; $j < $count; $j++) {
            if (is_array($tokens[$j]) && in_array($tokens[$j][0], $skip, true)) {
                continue;
            }

            $next = $tokens[$j];
            break;
        }

        if ($next === '(') {
            return true;
        }
    }

    return false;
}

function fileContainsToken(SplFileInfo $file, int $tokenType): bool
{
    foreach (token_get_all(File::get($file->getRealPath())) as $token) {
        if (is_array($token) && $token[0] === $tokenType) {
            return true;
        }
    }

    return false;
}

function modelClassFromFile(SplFileInfo $file): string
{
    return 'App\\Models\\' . str_replace(
        ['/', '.php'],
        ['\\', ''],
        mb_substr($file->getPathname(), mb_strlen(app_path('Models')) + 1)
    );
}

function modelFiles(): Illuminate\Support\Collection
{
    return collect(File::allFiles(app_path('Models')))
        ->filter(fn (SplFileInfo $file): bool => $file->getExtension() === 'php');
}

test('project php files declare strict types', function (): void {
    $files = collect([
        ...File::allFiles(app_path()),
        ...File::allFiles(base_path('config')),
        ...File::allFiles(database_path()),
        ...File::allFiles(public_path()),
        ...File::allFiles(base_path('routes')),
        ...File::allFiles(base_path('tests')),
        new SplFileInfo(base_path('bootstrap/app.php')),
        new SplFileInfo(base_path('bootstrap/providers.php')),
    ])->filter(fn (SplFileInfo $file): bool => $file->getExtension() === 'php' && ! str_ends_with($file->getFilename(), '.blade.php'));

    $files->each(function (SplFileInfo $file): void {
        $contents = File::get($file->getRealPath());
        $stripped = preg_replace('/\/\*.*?\*\/|\/\/[^\n]*|#[^\n]*/s', '', $contents) ?? $contents;

        expect(str_contains($stripped, 'declare(strict_types=1);'))
            ->toBeTrue('Expected strict types declaration in [' . relativeProjectPath($file) . '].');
    });
});

test('env is only used in config files', function (): void {
    $files = collect([
        ...File::allFiles(app_path()),
        ...File::allFiles(database_path()),
        ...File::allFiles(public_path()),
        ...File::allFiles(base_path('routes')),
        new SplFileInfo(base_path('bootstrap/app.php')),
        new SplFileInfo(base_path('bootstrap/providers.php')),
    ])->filter(fn (SplFileInfo $file): bool => $file->getExtension() === 'php' && ! str_ends_with($file->getFilename(), '.blade.php'));

    $files->each(function (SplFileInfo $file): void {
        expect(fileCallsAnyFunction($file, ['env']))
            ->toBeFalse('Unexpected env() usage in [' . relativeProjectPath($file) . '].');
    });
});

test('debug helpers are not committed into application code', function (): void {
    $files = collect([
        ...File::allFiles(app_path()),
        ...File::allFiles(base_path('routes')),
    ])->filter(fn (SplFileInfo $file): bool => $file->getExtension() === 'php');

    $files->each(function (SplFileInfo $file): void {
        expect(fileCallsAnyFunction($file, ['dd', 'dump', 'ray', 'var_dump']))
            ->toBeFalse('Unexpected debug helper found in [' . relativeProjectPath($file) . '].');
    });
});

test('project php docblocks do not carry type information', function (): void {
    $files = collect([
        ...File::allFiles(app_path()),
        ...File::allFiles(base_path('config')),
        ...File::allFiles(database_path()),
        ...File::allFiles(public_path()),
        ...File::allFiles(base_path('routes')),
        ...File::allFiles(base_path('tests')),
        new SplFileInfo(base_path('bootstrap/app.php')),
        new SplFileInfo(base_path('bootstrap/providers.php')),
    ])->filter(fn (SplFileInfo $file): bool => $file->getExtension() === 'php' && ! str_ends_with($file->getFilename(), '.blade.php'));

    $violations = [];

    $files->each(function (SplFileInfo $file) use (&$violations): void {
        $tokens = token_get_all(File::get($file->getRealPath()));

        foreach ($tokens as $token) {
            if ( ! is_array($token) || $token[0] !== T_DOC_COMMENT) {
                continue;
            }

            if (preg_match('/@(param|return|var)\b|array\s*\{/', $token[1]) !== 1) {
                continue;
            }

            $violations[] = relativeProjectPath($file);
        }
    });

    expect($violations)->toBe([]);
});

test('project php files do not use final', function (): void {
    $files = collect([
        ...File::allFiles(app_path()),
        ...File::allFiles(base_path('config')),
        ...File::allFiles(database_path()),
        ...File::allFiles(public_path()),
        ...File::allFiles(base_path('routes')),
        ...File::allFiles(base_path('tests')),
        new SplFileInfo(base_path('bootstrap/app.php')),
        new SplFileInfo(base_path('bootstrap/providers.php')),
    ])->filter(fn (SplFileInfo $file): bool => $file->getExtension() === 'php' && ! str_ends_with($file->getFilename(), '.blade.php'));

    $violations = [];

    $files->each(function (SplFileInfo $file) use (&$violations): void {
        if ( ! fileContainsToken($file, T_FINAL)) {
            return;
        }

        $violations[] = relativeProjectPath($file);
    });

    expect($violations)->toBe([]);
});

test('models use attribute-based configuration', function (): void {
    $requiredAttributes = [Fillable::class, Hidden::class];
    $forbiddenProperties = ['fillable', 'guarded', 'hidden', 'casts'];
    $inspected = 0;

    modelFiles()->each(function (SplFileInfo $file) use ($requiredAttributes, $forbiddenProperties, &$inspected): void {
        $class = modelClassFromFile($file);

        if ( ! class_exists($class)) {
            return;
        }

        $reflection = new ReflectionClass($class);

        if ( ! $reflection->isSubclassOf(Model::class)) {
            return;
        }

        $inspected++;

        foreach ($requiredAttributes as $attribute) {
            expect($reflection->getAttributes($attribute))
                ->not->toBeEmpty(sprintf(
                    'Model [%s] in [%s] must declare #[%s].',
                    $class,
                    relativeProjectPath($file),
                    class_basename($attribute)
                ));
        }

        if (in_array(HasFactory::class, class_uses_recursive($class), true)) {
            expect($reflection->getAttributes(UseFactory::class))
                ->not->toBeEmpty(sprintf(
                    'Model [%s] in [%s] uses HasFactory and must declare #[UseFactory].',
                    $class,
                    relativeProjectPath($file)
                ));
        }

        foreach ($reflection->getProperties() as $property) {
            if ($property->getDeclaringClass()->getName() !== $class) {
                continue;
            }

            expect(in_array($property->getName(), $forbiddenProperties, true))
                ->toBeFalse(sprintf(
                    'Model [%s] in [%s] must use attributes instead of protected $%s.',
                    $class,
                    relativeProjectPath($file),
                    $property->getName()
                ));
        }
    });

    expect($inspected)->toBeGreaterThan(0, 'Model convention rule did not reflect any models; the walk is a silent no-op.');
});

test('models do not use facades', function (): void {
    $violations = [];

    modelFiles()->each(function (SplFileInfo $file) use (&$violations): void {
        $tokens = token_get_all(File::get($file->getRealPath()));
        $count = count($tokens);

        for ($i = 0; $i < $count; $i++) {
            $token = $tokens[$i];

            if ( ! is_array($token)) {
                continue;
            }

            if ($token[0] !== T_NAME_QUALIFIED && $token[0] !== T_NAME_FULLY_QUALIFIED) {
                continue;
            }

            if ( ! str_starts_with(mb_ltrim($token[1], '\\'), 'Illuminate\\Support\\Facades\\')) {
                continue;
            }

            $violations[] = relativeProjectPath($file);

            return;
        }
    });

    expect($violations)->toBe([]);
});
