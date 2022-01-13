<?php

namespace Tests\Unit\Controllers\Vault\Contact\ViewHelpers;

use function env;
use Tests\TestCase;
use App\Models\User;
use App\Models\Vault;
use App\Models\Contact;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Http\Controllers\Vault\Contact\ViewHelpers\ContactIndexViewHelper;

class ContactIndexViewHelperTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_gets_the_data_needed_for_the_view(): void
    {
        $vault = Vault::factory()->create();
        $contact = Contact::factory()->create([
            'vault_id' => $vault->id,
        ]);
        $user = User::factory()->create();
        $contacts = Contact::all();
        $array = ContactIndexViewHelper::data($contacts, $user, $vault);

        $this->assertEquals(
            2,
            count($array)
        );

        $this->assertArrayHasKey('contacts', $array);
        $this->assertArrayHasKey('url', $array);

        $this->assertEquals(
            [
                0 => [
                    'id' => $contact->id,
                    'name' => $contact->getName($user),
                    'url' => [
                        'show' => env('APP_URL').'/vaults/'.$vault->id.'/contacts/'.$contact->id,
                    ],
                ],
            ],
            $array['contacts']->toArray()
        );

        $this->assertEquals(
            [
                'contact' => [
                    'create' => env('APP_URL').'/vaults/'.$vault->id.'/contacts/create',
                ],
            ],
            $array['url']
        );
    }
}
