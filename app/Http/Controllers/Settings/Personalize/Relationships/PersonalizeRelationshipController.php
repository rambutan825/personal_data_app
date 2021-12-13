<?php

namespace App\Http\Controllers\Settings\Personalize\Relationships;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Vault\ViewHelpers\VaultIndexViewHelper;
use App\Services\Account\ManageRelationshipTypes\CreateRelationshipGroupType;
use App\Services\Account\ManageRelationshipTypes\UpdateRelationshipGroupType;
use App\Services\Account\ManageRelationshipTypes\DestroyRelationshipGroupType;
use App\Http\Controllers\Settings\Personalize\Relationships\ViewHelpers\PersonalizeRelationshipIndexViewHelper;

class PersonalizeRelationshipController extends Controller
{
    public function index()
    {
        return Inertia::render('Settings/Personalize/Relationships/Index', [
            'layoutData' => VaultIndexViewHelper::layoutData(),
            'data' => PersonalizeRelationshipIndexViewHelper::data(Auth::user()->account),
        ]);
    }

    public function store(Request $request)
    {
        $data = [
            'account_id' => Auth::user()->account_id,
            'author_id' => Auth::user()->id,
            'name' => $request->input('groupTypeName'),
        ];

        $groupType = (new CreateRelationshipGroupType)->execute($data);

        return response()->json([
            'data' => PersonalizeRelationshipIndexViewHelper::dtoGroupType($groupType),
        ], 201);
    }

    public function update(Request $request, int $groupTypeId)
    {
        $data = [
            'account_id' => Auth::user()->account_id,
            'author_id' => Auth::user()->id,
            'relationship_group_type_id' => $groupTypeId,
            'name' => $request->input('groupTypeName'),
        ];

        $groupType = (new UpdateRelationshipGroupType)->execute($data);

        return response()->json([
            'data' => PersonalizeRelationshipIndexViewHelper::dtoGroupType($groupType),
        ], 200);
    }

    public function destroy(Request $request, int $groupTypeId)
    {
        $data = [
            'account_id' => Auth::user()->account_id,
            'author_id' => Auth::user()->id,
            'relationship_group_type_id' => $groupTypeId,
        ];

        (new DestroyRelationshipGroupType)->execute($data);

        return response()->json([
            'data' => true,
        ], 200);
    }
}
