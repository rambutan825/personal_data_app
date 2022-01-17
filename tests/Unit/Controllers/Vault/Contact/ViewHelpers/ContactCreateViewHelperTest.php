<?php

namespace Tests\Unit\Controllers\Vault\Contact\ViewHelpers;

use function env;
use Tests\TestCase;
use App\Models\Vault;
use App\Models\Gender;
use App\Models\Pronoun;
use App\Models\Template;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Http\Controllers\Vault\Contact\ViewHelpers\ContactCreateViewHelper;

class ContactCreateViewHelperTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_gets_the_data_needed_for_the_view(): void
    {
        $vault = Vault::factory()->create();
        $template = Template::factory()->create([
            'account_id' => $vault->account_id,
        ]);
        $vault->default_template_id = $template->id;
        $vault->save();
        $gender = Gender::factory()->create([
            'account_id' => $vault->account_id,
        ]);
        $pronoun = Pronoun::factory()->create([
            'account_id' => $vault->account_id,
        ]);
        $array = ContactCreateViewHelper::data($vault);

        $this->assertEquals(
            4,
            count($array)
        );

        $this->assertArrayHasKey('genders', $array);
        $this->assertArrayHasKey('pronouns', $array);
        $this->assertArrayHasKey('templates', $array);
        $this->assertArrayHasKey('url', $array);

        $this->assertEquals(
            [
                0 => [
                    'id' => $gender->id,
                    'name' => $gender->name,
                ],
            ],
            $array['genders']->toArray()
        );

        $this->assertEquals(
            [
                0 => [
                    'id' => $template->id,
                    'name' => $template->name,
                    'selected' => true,
                ],
            ],
            $array['templates']->toArray()
        );

        $this->assertEquals(
            [
                0 => [
                    'id' => $pronoun->id,
                    'name' => $pronoun->name,
                ],
            ],
            $array['pronouns']->toArray()
        );

        $this->assertEquals(
            [
                'store' => env('APP_URL').'/vaults/'.$vault->id.'/contacts',
                'back' => env('APP_URL').'/vaults/'.$vault->id.'/contacts',
            ],
            $array['url']
        );
    }
}
