<?php

namespace App\Features\Vault\ManageVaultUsers\Services;

use App\Models\User;
use App\Models\Vault;
use App\Models\Contact;
use App\Helpers\VaultHelper;
use App\Jobs\CreateAuditLog;
use App\Services\BaseService;
use App\Interfaces\ServiceInterface;
use App\Exceptions\SameUserException;
use App\Exceptions\MaximumNumberOfUsersInVaultException;

class GrantVaultAccessToUser extends BaseService implements ServiceInterface
{
    private User $user;
    private array $data;

    /**
     * Get the validation rules that apply to the service.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'account_id' => 'required|integer|exists:accounts,id',
            'author_id' => 'required|integer|exists:users,id',
            'vault_id' => 'required|integer|exists:vaults,id',
            'user_id' => 'required|integer|exists:users,id',
            'permission' => 'required|integer',
        ];
    }

    /**
     * Get the permissions that apply to the user calling the service.
     *
     * @return array
     */
    public function permissions(): array
    {
        return [
            'author_must_belong_to_account',
            'vault_must_belong_to_account',
            'author_must_be_vault_manager',
        ];
    }

    /**
     * Grant the access to the given vault to the given user.
     *
     * @param  array  $data
     */
    public function execute(array $data): void
    {
        $this->data = $data;
        $this->validate();
        $this->grant();

        $this->log();
    }

    private function validate(): void
    {
        $this->validateRules($this->data);

        $this->user = User::where('account_id', $this->data['account_id'])
            ->findOrFail($this->data['user_id']);

        if ($this->user->id === $this->author->id) {
            throw new SameUserException();
        }

        if ($this->vault->type === Vault::TYPE_PERSONAL && $this->vault->users->count() > 1) {
            throw new MaximumNumberOfUsersInVaultException();
        }
    }

    private function grant(): void
    {
        $contact = Contact::create([
            'vault_id' => $this->vault->id,
            'first_name' => $this->author->first_name,
            'last_name' => $this->author->last_name,
            'can_be_deleted' => false,
        ]);

        $this->vault->users()->save($this->user, [
            'permission' => $this->data['permission'],
            'contact_id' => $contact->id,
        ]);
    }

    private function log(): void
    {
        CreateAuditLog::dispatch([
            'account_id' => $this->author->account_id,
            'author_id' => $this->author->id,
            'author_name' => $this->author->name,
            'action_name' => 'vault_access_grant',
            'objects' => json_encode([
                'user_name' => $this->user->name,
                'vault_name' => $this->vault->name,
                'permission_type' => VaultHelper::getPermissionFriendlyName($this->data['permission']),
            ]),
        ]);
    }
}
