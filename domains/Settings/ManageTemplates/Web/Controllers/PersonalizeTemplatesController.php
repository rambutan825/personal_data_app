<?php

namespace App\Settings\ManageTemplates\Web\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Template;
use App\Settings\ManageTemplates\Services\CreateTemplate;
use App\Settings\ManageTemplates\Services\DestroyTemplate;
use App\Settings\ManageTemplates\Services\UpdateTemplate;
use App\Settings\ManageTemplates\Web\ViewHelpers\PersonalizeTemplateIndexViewHelper;
use App\Settings\ManageTemplates\Web\ViewHelpers\PersonalizeTemplateShowViewHelper;
use App\Vault\ManageVault\Web\ViewHelpers\VaultIndexViewHelper;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class PersonalizeTemplatesController extends Controller
{
    public function index()
    {
        return Inertia::render('Settings/Personalize/Templates/Index', [
            'layoutData' => VaultIndexViewHelper::layoutData(),
            'data' => PersonalizeTemplateIndexViewHelper::data(Auth::user()->account),
        ]);
    }

    public function store(Request $request)
    {
        $data = [
            'account_id' => Auth::user()->account_id,
            'author_id' => Auth::user()->id,
            'name' => $request->input('name'),
        ];

        $template = (new CreateTemplate)->execute($data);

        return response()->json([
            'data' => PersonalizeTemplateIndexViewHelper::dtoTemplate($template),
        ], 201);
    }

    public function update(Request $request, int $templateId)
    {
        $data = [
            'account_id' => Auth::user()->account_id,
            'author_id' => Auth::user()->id,
            'template_id' => $templateId,
            'name' => $request->input('name'),
        ];

        $template = (new UpdateTemplate)->execute($data);

        return response()->json([
            'data' => PersonalizeTemplateIndexViewHelper::dtoTemplate($template),
        ], 200);
    }

    public function destroy(Request $request, int $templateId)
    {
        $data = [
            'account_id' => Auth::user()->account_id,
            'author_id' => Auth::user()->id,
            'template_id' => $templateId,
        ];

        (new DestroyTemplate)->execute($data);

        return response()->json([
            'data' => true,
        ], 200);
    }

    public function show(Request $request, int $templateId)
    {
        try {
            $template = Template::where('account_id', Auth::user()->account_id)
                ->findOrFail($templateId);
        } catch (ModelNotFoundException) {
            return redirect('vaults');
        }

        return Inertia::render('Settings/Personalize/Templates/Show', [
            'layoutData' => VaultIndexViewHelper::layoutData(),
            'data' => PersonalizeTemplateShowViewHelper::data($template),
        ]);
    }
}
