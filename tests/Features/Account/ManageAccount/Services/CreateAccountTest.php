<?php

namespace Tests\Features\Account\ManageAccount\Services;

use Tests\TestCase;
use App\Models\User;
use App\Jobs\SetupAccount;
use App\Jobs\CreateAuditLog;
use Illuminate\Support\Facades\Queue;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Features\Account\ManageAccount\Services\CreateAccount;

class CreateAccountTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_creates_an_account(): void
    {
        $this->executeService();
    }

    /** @test */
    public function it_fails_if_wrong_parameters_are_given(): void
    {
        $request = [
            'title' => 'Ross',
        ];

        $this->expectException(ValidationException::class);
        (new CreateAccount)->execute($request);
    }

    private function executeService(): void
    {
        Queue::fake();

        $request = [
            'first_name' => 'john',
            'last_name' => 'john',
            'email' => 'john@email.com',
            'password' => 'john',
        ];

        $user = (new CreateAccount)->execute($request);

        $this->assertDatabaseHas('accounts', [
            'id' => $user->account->id,
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'account_id' => $user->account_id,
            'first_name' => 'john',
            'last_name' => 'john',
            'email' => 'john@email.com',
            'is_account_administrator' => true,
        ]);

        $this->assertInstanceOf(
            User::class,
            $user
        );

        Queue::assertPushed(SetupAccount::class, function ($job) use ($user) {
            return $job->user === $user;
        });

        Queue::assertPushed(CreateAuditLog::class, function ($job) {
            return $job->auditLog['action_name'] === 'account_created';
        });
    }
}
