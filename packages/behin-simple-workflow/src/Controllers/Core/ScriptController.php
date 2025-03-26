<?php

namespace Behin\SimpleWorkflow\Controllers\Core;

use App\Http\Controllers\Controller;
use Behin\SimpleWorkflow\Models\Core\Form;
use Behin\SimpleWorkflow\Models\Core\Process;
use Behin\SimpleWorkflow\Models\Core\Script;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ScriptController extends Controller
{
    public function index()
    {
        $scripts = Script::orderBy('created_at', 'desc')->get();
        return view('SimpleWorkflowView::Core.Script.index', compact('scripts'));
    }

    public function create()
    {
        return view('SimpleWorkflowView::Core.Script.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'executive_file' => 'nullable|string',
            'content' => 'nullable|json',
        ]);
        if($request->executive_file){
            $filePath = base_path('packages/behin-simple-workflow/src/Controllers/Scripts/' . $request->executive_file . '.php');
            if(!file_exists($filePath)){
                file_put_contents($filePath, '<?php');
            }
        }

        Script::updateOrCreate($request->only('name'), $request->only('executive_file'));

        return redirect()->route('simpleWorkflow.scripts.index')->with('success', 'Script created successfully.');
    }

    public function edit(Script $script)
    {
        return view('SimpleWorkflowView::Core.Script.edit', compact('script'));
    }

    public function update(Request $request, Script $script)
    {
        // $request->validate([
        //     'name' => 'required|string|max:255',
        //     'executive_file' => 'nullable|string',
        //     'content' => 'nullable|json',
        // ]);

        if ($request->executive_file_content) {
            $file = base_path('packages/behin-simple-workflow/src/Controllers/Scripts/' . $script->executive_file . '.php');
            file_put_contents($file, $request->executive_file_content);
            return redirect()->route('simpleWorkflow.scripts.edit', $script->id)->with('success', 'Script updated successfully.');
        }

        $script->update($request->only('name', 'executive_file', 'content'));

        return redirect()->route('simpleWorkflow.scripts.index')->with('success', 'Script updated successfully.');
    }

    public static function getAll()
    {
        return Script::get();
    }
    public static function getById($id)
    {
        return Script::find($id);
    }

    public static function runScript($id, $caseId = null, $forTest = false)
    {
        return DB::transaction(function () use ($id, $caseId, $forTest) {
            $script = self::getById($id);
            $case = CaseController::getById($caseId);
            $executiveFile = "\\Behin\SimpleWorkflow\Controllers\Scripts\\$script->executive_file";
            $script = new $executiveFile($case);
            $output = $script->execute();
            if ($forTest) {
                return throw new \Exception($output);
            }
            return $output;
        });
        // $script = self::getById($id);
        // $case = CaseController::getById($caseId);
        // $executiveFile = "\\Behin\SimpleWorkflow\Controllers\Scripts\\$script->executive_file";
        // $script = new $executiveFile($case);
        // return $script->execute();
    }

    public static function test(Request $request, $id)
    {
        try {
            $result = self::runScript($id, $request->caseId, true);
        } catch (\Exception $e) {
            $result = $e->getMessage();
        }
        return $result;
    }

    public static function runFromView(Request $request, $id)
    {
        return DB::transaction(function () use ($id, $request) {
            $script = self::getById($id);
            $executiveFile = "\\Behin\SimpleWorkflow\Controllers\Scripts\\$script->executive_file";
            $script = new $executiveFile();
            $output = $script->execute($request);
            return $output;
        });
    }
}
