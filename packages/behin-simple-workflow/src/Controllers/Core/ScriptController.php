<?php

namespace Behin\SimpleWorkflow\Controllers\Core;

use App\Http\Controllers\Controller;
use Behin\SimpleWorkflow\Models\Core\Form;
use Behin\SimpleWorkflow\Models\Core\Process;
use Behin\SimpleWorkflow\Models\Core\Script;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use OpenAI;

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
            'content' => 'nullable|string',
        ]);
        $content = $request->content;
        if ($request->executive_file) {
            $filePath = base_path('packages/behin-simple-workflow/src/Controllers/Scripts/' . $request->executive_file . '.php');
            if (!file_exists($filePath)) {
                file_put_contents($filePath, '<?php');
                $content = '<?php';
            } elseif (!$content) {
                $content = file_get_contents($filePath);
            }
        }

        Script::updateOrCreate(
            ['name' => $request->name],
            ['executive_file' => $request->executive_file, 'content' => $content]
        );

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
            $script->update(['content' => $request->executive_file_content]);
            return redirect()->route('simpleWorkflow.scripts.edit', $script->id)->with('success', 'Script updated successfully.');
        }

        $script->update($request->only('name', 'executive_file', 'content'));

        return redirect()->route('simpleWorkflow.scripts.index')->with('success', 'Script updated successfully.');
    }

    public function export(Request $request)
    {
        $ids = $request->input('script_ids', []);
        if (empty($ids)) {
            return redirect()->route('simpleWorkflow.scripts.index')->with('error', 'No scripts selected for export.');
        }

        $scripts = Script::whereIn('id', $ids)->get();
        $fileName = 'scripts-' . date('Ymd_His') . '.json';

        if ($scripts->count() === 1) {
            $content = $scripts->first()->toJson(JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        } else {
            $content = $scripts->toJson(JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }

        return response()->streamDownload(function () use ($content) {
            echo $content;
        }, $fileName);
    }

    public function import(Request $request)
    {
        $request->validate([
            'scripts_file' => 'required|file',
        ]);

        $content = file_get_contents($request->file('scripts_file')->getRealPath());
        $data = json_decode($content, true);

        if (is_null($data)) {
            return redirect()->route('simpleWorkflow.scripts.index')->with('error', 'Invalid import file.');
        }

        $scriptsData = isset($data[0]) ? $data : [$data];

        foreach ($scriptsData as $scriptData) {
            $scriptArray = [
                'id' => $scriptData['id'] ?? Str::uuid()->toString(),
                'name' => $scriptData['name'] ?? null,
                'executive_file' => $scriptData['executive_file'] ?? null,
                'content' => $scriptData['content'] ?? null,
            ];

            Script::updateOrCreate(['id' => $scriptArray['id']], $scriptArray);
        }

        return redirect()->route('simpleWorkflow.scripts.index')->with('success', 'Scripts imported successfully.');
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
            self::loadContent($script);
            $executiveFile = "\\Behin\SimpleWorkflow\Controllers\Scripts\\$script->executive_file";
            $scriptInstance = new $executiveFile($case);
            $output = $scriptInstance->execute();
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
            self::loadContent($script);
            $executiveFile = "\\Behin\SimpleWorkflow\Controllers\Scripts\\$script->executive_file";
            $scriptInstance = new $executiveFile();
            $output = $scriptInstance->execute($request);
            return $output;
        });
    }

    private static function loadContent(Script $script)
    {
        $executiveClass = "Behin\\SimpleWorkflow\\Controllers\\Scripts\\" . $script->executive_file . '.php';

        if (!$script->content && $script->executive_file) {
            $filePath = base_path('packages/behin-simple-workflow/src/Controllers/Scripts/' . $script->executive_file . '.php');
            if (file_exists($filePath)) {
                $content = file_get_contents($filePath);
                $script->update(['content' => $content]);
            }
        }

        if ($script->content && !class_exists($executiveClass)) {
            $code = preg_replace('/^<\?php\s*/', '', $script->content);
            eval($code);
        }
    }

    public function autocomplete(Request $request)
    {
        return response()->json([
            'suggestion' => ''
        ]);
        $client = OpenAI::factory()
        ->withApiKey(env('OPENAI_API_KEY'))
        ->withHttpClient(new \GuzzleHttp\Client([
            'verify' => false, // ⛔ فقط برای تست
        ]))
        ->make();

        $response = $client->chat()->create([
            'model' => 'gpt-4o-mini',
            'messages' => [
                ['role' => 'system', 'content' => 'You are a code autocomplete assistant for PHP code.'],
                ['role' => 'user', 'content' => $request->code]
            ],
        ]);

        return response()->json([
            'suggestion' => trim($response->choices[0]->message->content ?? '')
        ]);
    }
}
