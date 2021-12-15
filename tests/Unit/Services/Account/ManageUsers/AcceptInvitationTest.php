<?php

namespace Tests\Unit\Services\Account\ManageUsers;

use Carbon\Carbon;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Services\Account\ManageUsers\AcceptInvitation;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AcceptInvitationTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_accepts_the_invitation_from_another_user(): void
    {
        $user = User::factory()->create([
            'invitation_code' => (string) Str::uuid(),
        ]);
        $this->executeService($user);
    }

    /** @test */
    public function it_fails_if_user_doesnt_have_a_valid_invitation_code(): void
    {
        $user = User::factory()->create([
            'invitation_code' => '123',
        ]);
        $this->expectException(ValidationException::class);
        $this->executeService($user);
    }

    /** @test */
    public function it_fails_if_user_has_already_accepted_invitation(): void
    {
        $user = User::factory()->create([
            'invitation_code' => (string) Str::uuid(),
            'invitation_accepted_at' => Carbon::now(),
        ]);
        $this->expectException(ModelNotFoundException::class);
        $this->executeService($user);
    }

    /** @test */
    public function it_fails_if_wrong_parameters_are_given(): void
    {
        $request = [
            'title' => 'Ross',
        ];

        $this->expectException(ValidationException::class);
        (new AcceptInvitation)->execute($request);
    }

    private function executeService(User $user): void
    {
        Carbon::setTestNow(Carbon::create(2018, 1, 1));

        $request = [
            'invitation_code' => $user->invitation_code,
            'first_name' => 'James',
            'last_name' => 'Bond',
            'password' => 'password',
        ];

        $user = (new AcceptInvitation)->execute($request);

        $this->assertInstanceOf(
            User::class,
            $user
        );

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'is_account_administrator' => false,
            'first_name' => 'James',
            'last_name' => 'Bond',
            'invitation_accepted_at' => Carbon::now(),
            'email_verified_at' => Carbon::now(),
        ]);
    }
}
