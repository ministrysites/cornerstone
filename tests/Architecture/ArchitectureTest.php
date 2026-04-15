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

arch('app classes are not final')
    ->expect('App')
    ->not->toBeFinal();

arch('controllers extend the base controller')
    ->expect('App\Http\Controllers')
    ->classes()
    ->toExtend(Controller::class)
    ->ignoring(Controller::class);

arch('livewire components extend the livewire base component')
    ->expect('App\Livewire')
    ->classes()
    ->toExtend(Component::class);

arch('models do not use facades')
    ->expect('App\Models')
    ->not->toUse('Illuminate\Support\Facades');

arch('debug helpers are not committed into application code')
    ->expect(['dd', 'dump', 'ray', 'var_dump'])
    ->not->toBeUsed();

arch('env() is only called inside config files')
    ->expect('env')
    ->not->toBeUsed()
    ->ignoring('config');

test('public livewire properties are explicitly bound, locked, or synced', function (): void {
    $allowed = [Locked::class, Validate::class, Url::class, Modelable::class];
    $inspected = 0;

    foreach (livewireComponentClasses() as $class) {
        $reflection = new ReflectionClass($class);

        if ( ! $reflection->isSubclassOf(Component::class)) {
            continue;
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
                'Public Livewire property [%s::$%s] must be marked with #[Locked], #[Validate], #[Url], or #[Modelable].',
                $class,
                $property->getName()
            ));
        }
    }

    expect($inspected)->toBeGreaterThan(0, 'Livewire property rule did not reflect any public properties; the walk is a silent no-op.');
});

test('models use attribute-based configuration', function (): void {
    $requiredAttributes = [Fillable::class, Hidden::class];
    $forbiddenProperties = ['fillable', 'guarded', 'hidden', 'casts'];
    $inspected = 0;

    foreach (modelClasses() as $class) {
        $reflection = new ReflectionClass($class);

        if ( ! $reflection->isSubclassOf(Model::class)) {
            continue;
        }

        $inspected++;

        foreach ($requiredAttributes as $attribute) {
            expect($reflection->getAttributes($attribute))
                ->not->toBeEmpty(sprintf(
                    'Model [%s] must declare #[%s].',
                    $class,
                    class_basename($attribute)
                ));
        }

        if (in_array(HasFactory::class, class_uses_recursive($class), true)) {
            expect($reflection->getAttributes(UseFactory::class))
                ->not->toBeEmpty(sprintf('Model [%s] uses HasFactory and must declare #[UseFactory].', $class));
        }

        foreach ($reflection->getProperties() as $property) {
            if ($property->getDeclaringClass()->getName() !== $class) {
                continue;
            }

            expect(in_array($property->getName(), $forbiddenProperties, true))
                ->toBeFalse(sprintf(
                    'Model [%s] must use attributes instead of protected $%s.',
                    $class,
                    $property->getName()
                ));
        }
    }

    expect($inspected)->toBeGreaterThan(0, 'Model convention rule did not reflect any models; the walk is a silent no-op.');
});

test('docblocks do not carry type information', function (): void {
    $violations = [];

    foreach (projectPhpFiles() as $file) {
        foreach (token_get_all(File::get($file->getRealPath())) as $token) {
            if ( ! is_array($token) || $token[0] !== T_DOC_COMMENT) {
                continue;
            }

            if (preg_match('/@(param|return|var)\b|array\s*\{/', $token[1]) !== 1) {
                continue;
            }

            $violations[] = relativeProjectPath($file);
        }
    }

    expect($violations)->toBe([]);
});
