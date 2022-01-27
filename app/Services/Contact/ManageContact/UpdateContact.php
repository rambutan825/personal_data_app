<?php

namespace App\Services\Contact\ManageContact;

use App\Models\Gender;
use App\Models\Contact;
use App\Models\Pronoun;
use App\Jobs\CreateAuditLog;
use App\Services\BaseService;
use App\Jobs\CreateContactLog;
use App\Interfaces\ServiceInterface;

class UpdateContact extends BaseService implements ServiceInterface
{
    private array $data;
    private Gender $gender;
    private Pronoun $pronoun;

    /**
     * Get the validation rules that apply to the service.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'account_id' => 'required|integer|exists:accounts,id',
            'vault_id' => 'required|integer|exists:vaults,id',
            'author_id' => 'required|integer|exists:users,id',
            'contact_id' => 'required|integer|exists:contacts,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'nickname' => 'nullable|string|max:255',
            'maiden_name' => 'nullable|string|max:255',
            'gender_id' => 'nullable|integer|exists:genders,id',
            'pronoun_id' => 'nullable|integer|exists:pronouns,id',
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
            'contact_must_belong_to_vault',
            'author_must_be_vault_editor',
        ];
    }

    /**
     * Update a contact.
     *
     * @param  array  $data
     * @return Contact
     */
    public function execute(array $data): Contact
    {
        $this->data = $data;
        $this->validate();

        $this->contact->first_name = $data['first_name'];
        $this->contact->last_name = $this->valueOrNull($data, 'last_name');
        $this->contact->middle_name = $this->valueOrNull($data, 'middle_name');
        $this->contact->maiden_name = $this->valueOrNull($data, 'maiden_name');
        $this->contact->nickname = $this->valueOrNull($data, 'nickname');
        if ($this->valueOrNull($this->data, 'gender_id')) {
            $this->contact->gender_id = $this->gender->id;
        } else {
            $this->contact->gender_id = null;
        }
        if ($this->valueOrNull($this->data, 'pronoun_id')) {
            $this->contact->pronoun_id = $this->pronoun->id;
        } else {
            $this->contact->pronoun_id = null;
        }
        $this->contact->save();

        $this->log();

        return $this->contact;
    }

    private function validate(): void
    {
        $this->validateRules($this->data);

        if ($this->valueOrNull($this->data, 'gender_id')) {
            $this->gender = Gender::where('account_id', $this->data['account_id'])
                ->findOrFail($this->data['gender_id']);
        }

        if ($this->valueOrNull($this->data, 'pronoun_id')) {
            $this->pronoun = Pronoun::where('account_id', $this->data['account_id'])
                ->findOrFail($this->data['pronoun_id']);
        }
    }

    private function log(): void
    {
        CreateAuditLog::dispatch([
            'account_id' => $this->author->account_id,
            'author_id' => $this->author->id,
            'author_name' => $this->author->name,
            'action_name' => 'contact_updated',
            'objects' => json_encode([
                'id' => $this->contact->id,
                'name' => $this->contact->name,
            ]),
        ]);

        CreateContactLog::dispatch([
            'contact_id' => $this->contact->id,
            'author_id' => $this->author->id,
            'author_name' => $this->author->name,
            'action_name' => 'contact_updated',
            'objects' => json_encode([]),
        ]);
    }
}
