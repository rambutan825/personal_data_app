<?php

namespace Tests\Unit\Services\Account\ManageRelationshipTypes;

use Tests\TestCase;
use App\Models\User;
use App\Models\Account;
use App\Jobs\CreateAuditLog;
use App\Models\RelationshipType;
use App\Models\RelationshipGroupType;
use Illuminate\Support\Facades\Queue;
use Illuminate\Validation\ValidationException;
use App\Exceptions\NotEnoughPermissionException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Services\Account\ManageRelationshipTypes\CreateRelationshipType;

class CreateRelationshipTypeTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_creates_a_relationship_type(): void
    {
        $ross = $this->createAdministrator();
        $group = RelationshipGroupType::factory()->create([
            'account_id' => $ross->account->id,
        ]);
        $this->executeService($ross, $ross->account, $group);
    }

    /** @test */
    public function it_fails_if_wrong_parameters_are_given(): void
    {
        $request = [
            'title' => 'Ross',
        ];

        $this->expectException(ValidationException::class);
        (new CreateRelationshipType)->execute($request);
    }

    /** @test */
    public function it_fails_if_user_doesnt_belong_to_account(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $ross = $this->createAdministrator();
        $account = $this->createAccount();
        $group = RelationshipGroupType::factory()->create([
            'account_id' => $ross->account->id,
        ]);
        $this->executeService($ross, $account, $group);
    }

    /** @test */
    public function it_fails_if_user_is_not_administrator(): void
    {
        $this->expectException(NotEnoughPermissionException::class);

        $ross = $this->createUser();
        $group = RelationshipGroupType::factory()->create([
            'account_id' => $ross->account->id,
        ]);
        $this->executeService($ross, $ross->account, $group);
    }

    private function executeService(User $author, Account $account, RelationshipGroupType $groupType): void
    {
        Queue::fake();

        $request = [
            'account_id' => $account->id,
            'author_id' => $author->id,
            'relationship_group_type_id' => $groupType->id,
            'name' => 'type name',
            'name_reverse_relationship' => 'reverse type name',
        ];

        $type = (new CreateRelationshipType)->execute($request);

        $this->assertDatabaseHas('relationship_types', [
            'id' => $type->id,
            'relationship_group_type_id' => $groupType->id,
            'name' => 'type name',
            'name_reverse_relationship' => 'reverse type name',
        ]);

        $this->assertInstanceOf(
            RelationshipType::class,
            $type
        );

        Queue::assertPushed(CreateAuditLog::class, function ($job) {
            return $job->auditLog['action_name'] === 'relationship_type_created';
        });
    }
}
