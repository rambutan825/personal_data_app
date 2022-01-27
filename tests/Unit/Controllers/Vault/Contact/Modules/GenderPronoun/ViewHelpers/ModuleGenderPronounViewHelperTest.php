<?php

namespace Tests\Unit\Controllers\Vault\Contact\Modules\GenderPronoun\ViewHelpers;

use Tests\TestCase;
use App\Models\Gender;
use App\Models\Contact;
use App\Models\Pronoun;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Http\Controllers\Vault\Contact\Modules\GenderPronoun\ViewHelpers\ModuleGenderPronounViewHelper;

class ModuleGenderPronounViewHelperTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_gets_the_data_needed_for_the_view(): void
    {
        $gender = Gender::factory()->create([
            'name' => 'gender',
        ]);
        $pronoun = Pronoun::factory()->create([
            'name' => 'pronoun',
        ]);
        $contact = Contact::factory()->create([
            'pronoun_id' => $pronoun->id,
            'gender_id' => $gender->id,
        ]);

        $array = ModuleGenderPronounViewHelper::data($contact);

        $this->assertEquals(
            3,
            count($array)
        );

        $this->assertArrayHasKey('gender', $array);
        $this->assertArrayHasKey('pronoun', $array);

        $this->assertEquals(
            [
                'gender' => 'gender',
                'pronoun' => 'pronoun',
                'url' => [
                    'edit' => env('APP_URL').'/vaults/'.$contact->vault->id.'/contacts/'.$contact->id.'/edit',
                ],
            ],
            $array
        );
    }
}
