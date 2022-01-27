<?php

namespace App\Http\Controllers\Vault\Contact\Modules\GenderPronoun\ViewHelpers;

use App\Models\Contact;

class ModuleGenderPronounViewHelper
{
    public static function data(Contact $contact): array
    {
        return [
            'gender' => $contact->gender ? $contact->gender->name : null,
            'pronoun' => $contact->pronoun ? $contact->pronoun->name : null,
            'url' => [
                'edit' => route('contact.edit', [
                    'vault' => $contact->vault_id,
                    'contact' => $contact->id,
                ]),
            ],
        ];
    }
}
