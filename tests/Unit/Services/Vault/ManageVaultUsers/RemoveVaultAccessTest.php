<?php

namespace Tests\Unit\Services\Vault\ManageVaultUsers;

use Tests\TestCase;
use App\Models\User;
use App\Models\Vault;
use App\Models\Account;
use App\Models\Contact;
use App\Jobs\CreateAuditLog;
use Illuminate\Support\Facades\Queue;
use Illuminate\Validation\ValidationException;
use App\Exceptions\NotEnoughPermissionException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Services\Vault\ManageVaultUsers\RemoveVaultAccess;

class RemoveVaultAccessTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_removes_the_permission_in_the_vault(): void
    {
        $regis = $this->createAdministrator();
        $anotherUser = User::factory()->create([
            'account_id' => $regis->account_id,
        ]);
        $vault = $this->createVault($regis->account);
        $vault = $this->setPermissionInVault($regis, Vault::PERMISSION_MANAGE, $vault);
        $contact = Contact::factory()->create();
        $vault->users()->save($anotherUser, [
            'permission' => Vault::PERMISSION_MANAGE,
            'contact_id' => $contact->id,
        ]);
        $this->executeService($regis->account, $regis, $anotherUser, $vault, $contact);
    }

    /** @test */
    public function it_fails_if_user_doesnt_belong_to_account(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $regis = $this->createAdministrator();
        $account = Account::factory()->create();
        $anotherUser = User::factory()->create([
            'account_id' => $regis->account_id,
        ]);
        $vault = $this->createVault($regis->account);
        $vault = $this->setPermissionInVault($regis, Vault::PERMISSION_MANAGE, $vault);
        $contact = Contact::factory()->create();
        $vault->users()->save($anotherUser, [
            'permission' => Vault::PERMISSION_MANAGE,
            'contact_id' => $contact->id,
        ]);
        $this->executeService($account, $regis, $anotherUser, $vault, $contact);
    }

    /** @test */
    public function it_fails_if_the_other_user_doesnt_belong_to_account(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $regis = $this->createAdministrator();
        $anotherUser = User::factory()->create();
        $vault = $this->createVault($regis->account);
        $vault = $this->setPermissionInVault($regis, Vault::PERMISSION_MANAGE, $vault);
        $contact = Contact::factory()->create();
        $vault->users()->save($anotherUser, [
            'permission' => Vault::PERMISSION_MANAGE,
            'contact_id' => $contact->id,
        ]);
        $this->executeService($regis->account, $regis, $anotherUser, $vault, $contact);
    }

    /** @test */
    public function it_fails_if_user_is_not_vault_manager(): void
    {
        $this->expectException(NotEnoughPermissionException::class);

        $regis = $this->createUser();
        $anotherUser = User::factory()->create([
            'account_id' => $regis->account_id,
        ]);
        $vault = $this->createVault($regis->account);
        $vault = $this->setPermissionInVault($regis, Vault::PERMISSION_EDIT, $vault);
        $contact = Contact::factory()->create();
        $vault->users()->save($anotherUser, [
            'permission' => Vault::PERMISSION_MANAGE,
            'contact_id' => $contact->id,
        ]);
        $this->executeService($regis->account, $regis, $anotherUser, $vault, $contact);
    }

    /** @test */
    public function it_fails_if_wrong_parameters_are_given(): void
    {
        $request = [
            'title' => 'Ross',
        ];

        $this->expectException(ValidationException::class);
        (new RemoveVaultAccess)->execute($request);
    }

    private function executeService(Account $account, User $regis, User $anotherUser, Vault $vault, Contact $contact): void
    {
        Queue::fake();

        $request = [
            'account_id' => $account->id,
            'author_id' => $regis->id,
            'vault_id' => $vault->id,
            'user_id' => $anotherUser->id,
        ];

        (new RemoveVaultAccess)->execute($request);

        $this->assertDatabaseMissing('user_vault', [
            'vault_id' => $vault->id,
            'user_id' => $anotherUser->id,
            'permission' => Vault::PERMISSION_VIEW,
        ]);

        $this->assertDatabaseMissing('contacts', [
            'id' => $contact->id,
        ]);

        Queue::assertPushed(CreateAuditLog::class, function ($job) {
            return $job->auditLog['action_name'] === 'vault_access_removed';
        });
    }
}
