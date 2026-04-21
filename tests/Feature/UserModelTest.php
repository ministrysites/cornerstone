<?php

declare(strict_types=1);

use App\Models\Model;
use App\Models\User;
use Glhd\Bits\Snowflake;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('base app models generate snowflake primary keys', function (): void {
    $model = new class () extends Model {};
    $id = $model->newUniqueId();

    assert(is_int($id));

    expect($model->getIncrementing())->toBeFalse()
        ->and($id > 1)->toBeTrue()
        ->and(Snowflake::fromId($id)->id())->toBe($id);
});

test('users are created with snowflake primary keys', function (): void {
    $user = User::factory()->create();
    $id = $user->getKey();

    assert(is_int($id));

    expect($user->getIncrementing())->toBeFalse()
        ->and($id > 1)->toBeTrue()
        ->and(Snowflake::fromId($id)->id())->toBe($id);
});
