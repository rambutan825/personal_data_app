<?php

namespace App\Http\Controllers\Settings\Personalize\ViewHelpers;

class PersonalizeIndexViewHelper
{
    public static function data(): array
    {
        return [
            'url' => [
                'settings' => route('settings.index'),
                'back' => route('settings.index'),
                'manage_relationships' => route('settings.personalize.relationship.index'),
                'manage_labels' => route('settings.personalize.label.index'),
                'manage_genders' => route('settings.personalize.gender.index'),
                'manage_pronouns' => route('settings.personalize.pronoun.index'),
                'manage_address_types' => route('settings.personalize.address_type.index'),
                'manage_pet_categories' => route('settings.personalize.pet_category.index'),
                'manage_contact_information_types' => route('settings.personalize.contact_information_type.index'),
            ],
        ];
    }
}
