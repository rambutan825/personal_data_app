<?php

namespace Tests\Unit\Controllers\Settings\ViewHelpers;

use function env;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Http\Controllers\Settings\ViewHelpers\SettingsIndexViewHelper;

class SettingsIndexViewHelperTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_gets_the_data_needed_for_the_view(): void
    {
        $user = User::factory()->create([
            'is_account_administrator' => true,
        ]);
        $array = SettingsIndexViewHelper::data($user);
        $this->assertEquals(
            [
                'is_account_administrator' => true,
                'url' => [
                    'preferences' => [
                        'index' => env('APP_URL').'/settings/preferences',
                    ],
                    'users' => [
                        'index' => env('APP_URL').'/settings/users',
                    ],
                    'personalize' => [
                        'index' => env('APP_URL').'/settings/personalize',
                    ],
                    'cancel' => [
                        'index' => env('APP_URL').'/settings/cancel',
                    ],
                ],
            ],
            $array
        );
    }
}
