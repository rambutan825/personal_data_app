<?php

namespace Tests\Unit\Controllers\Settings\Personalize\ContactInformationTypes\ViewHelpers;

use function env;
use Tests\TestCase;
use App\Models\ContactInformationType;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Http\Controllers\Settings\Personalize\ContactInformationTypes\ViewHelpers\PersonalizeContactInformationTypeIndexViewHelper;

class PersonalizeContactInformationTypeIndexViewHelperTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_gets_the_data_needed_for_the_view(): void
    {
        $contactInformationType = ContactInformationType::factory()->create();
        $array = PersonalizeContactInformationTypeIndexViewHelper::data($contactInformationType->account);
        $this->assertEquals(
            2,
            count($array)
        );
        $this->assertArrayHasKey('contact_information_types', $array);
        $this->assertEquals(
            [
                'settings' => env('APP_URL').'/settings',
                'personalize' => env('APP_URL').'/settings/personalize',
                'contact_information_type_store' => env('APP_URL').'/settings/personalize/contactInformationType',
            ],
            $array['url']
        );
    }

    /** @test */
    public function it_gets_the_data_needed_for_the_data_transfer_object(): void
    {
        $contactInformationType = ContactInformationType::factory()->create();
        $array = PersonalizeContactInformationTypeIndexViewHelper::dtoContactInformationType($contactInformationType);
        $this->assertEquals(
            [
                'id' => $contactInformationType->id,
                'name' => $contactInformationType->name,
                'protocol' => $contactInformationType->protocol,
                'can_be_deleted' => $contactInformationType->can_be_deleted,
                'url' => [
                    'update' => env('APP_URL').'/settings/personalize/contactInformationType/'.$contactInformationType->id,
                    'destroy' => env('APP_URL').'/settings/personalize/contactInformationType/'.$contactInformationType->id,
                ],
            ],
            $array
        );
    }
}
