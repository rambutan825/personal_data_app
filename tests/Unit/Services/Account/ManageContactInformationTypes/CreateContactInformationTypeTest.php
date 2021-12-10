<?php

namespace Tests\Unit\Services\Account\ManageContactInformationTypes;

use Tests\TestCase;
use App\Models\User;
use App\Models\Account;
use App\Jobs\CreateAuditLog;
use Illuminate\Support\Facades\Queue;
use App\Models\ContactInformationType;
use Illuminate\Validation\ValidationException;
use App\Exceptions\NotEnoughPermissionException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Services\Account\ManageContactInformationTypes\CreateContactInformationType;

class CreateContactInformationTypeTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_creates_a_contact_information_type(): void
    {
        $ross = $this->createAdministrator();
        $this->executeService($ross, $ross->account);
    }

    /** @test */
    public function it_fails_if_wrong_parameters_are_given(): void
    {
        $request = [
            'title' => 'Ross',
        ];

        $this->expectException(ValidationException::class);
        (new CreateContactInformationType)->execute($request);
    }

    /** @test */
    public function it_fails_if_user_doesnt_belong_to_account(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $ross = $this->createAdministrator();
        $account = $this->createAccount();
        $this->executeService($ross, $account);
    }

    /** @test */
    public function it_fails_if_user_is_not_administrator(): void
    {
        $this->expectException(NotEnoughPermissionException::class);

        $ross = $this->createUser();
        $this->executeService($ross, $ross->account);
    }

    private function executeService(User $author, Account $account): void
    {
        Queue::fake();

        $request = [
            'account_id' => $account->id,
            'author_id' => $author->id,
            'name' => 'type name',
        ];

        $type = (new CreateContactInformationType)->execute($request);

        $this->assertDatabaseHas('contact_information_types', [
            'id' => $type->id,
            'account_id' => $account->id,
            'name' => 'type name',
        ]);

        $this->assertInstanceOf(
            ContactInformationType::class,
            $type
        );

        Queue::assertPushed(CreateAuditLog::class, function ($job) {
            return $job->auditLog['action_name'] === 'contact_information_type_created';
        });
    }
}
