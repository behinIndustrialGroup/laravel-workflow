<?php

namespace Behin\SimpleWorkflow\Controllers\Core;

use App\Http\Controllers\Controller;
use Behin\SimpleWorkflow\Models\Core\Entity;
use Behin\SimpleWorkflow\Models\Core\Fields;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EntityController extends Controller
{
    public function index()
    {
        $entities = self::getAll();
        return view('SimpleWorkflowView::Core.Entity.index', compact('entities'));
    }

    public function create()
    {
        return view('SimpleWorkflowView::Core.Condition.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Entity::create([
            'name' => $request->name,
        ]);

        return redirect()->route('simpleWorkflow.entities.index')->with('success', 'Entity created successfully.');
    }

    public function edit(Entity $entity)
    {
        $tables = collect(DB::select('SHOW TABLES'))->map(function ($row) {
            return array_values((array) $row)[0];
        })->toArray();

        return view('SimpleWorkflowView::Core.Entity.edit', compact('entity', 'tables'));
    }

    public function update(Request $request, Entity $entity)
    {
        $defaultUses = "use Behin\\SimpleWorkflow\\Controllers\\Core\\VariableController; use Illuminate\\Database\\Eloquent\\Factories\\HasFactory; use Illuminate\\Database\\Eloquent\\Model; use Illuminate\\Support\\Str; use Illuminate\\Database\\Eloquent\\SoftDeletes;";
        $uses = $request->uses ?? $defaultUses;
        $classContents = $request->class_contents ?? '';
        // $classContents = preg_replace(
        //     "/\r?\n\s*public function \w+\(\)\s*\r?\n\s*{\s*\r?\n\s*return \\$this->belongsTo\([^;]+;\s*\r?\n\s*}\s*/",
        //     "\n",
        //     $classContents
        // );

        $relations = '';
        $columnsLines = preg_split('/\r\n|\n|\r/', trim($request->columns));
        foreach ($columnsLines as $line) {
            if (!$line) {
                continue;
            }
            $details = explode(',', $line);
            $name = $details[0];
            $type = $details[1] ?? '';
            if (Str::startsWith($type, 'entity:')) {
                $table = substr($type, 7);
                $method = Str::camel(str_replace(['_id'], '', $name));
                $relations .= "\n    public function $method()\n    {\n        return \$this->belongsTo('$table', '$name');\n    }\n";
            }
        }
        $classContents .= $relations;

        $entity->update([
            'name' => $request->name,
            'description' => $request->description,
            'columns' => $request->columns,
            'uses' => $uses,
            'class_contents' => $classContents,
        ]);

        return redirect()->route('simpleWorkflow.entities.edit', $entity->id)->with('success', 'Entity updated successfully.');
    }

    public static function getAll()
    {
        return Entity::orderBy('created_at')->get();
    }
    public static function getById($id)
    {
        return Fields::find($id);
    }

    public static function getByName($fieldName)
    {
        return Fields::where('name', $fieldName)->first();
    }

    public static function createTable(Entity $entity)
    {
        // $columns = str_replace('\r', '', $entity->columns);
        $columns = explode("\n", $entity->columns);
        $entity->db_table_name = 'wf_entity_' . $entity->name;
        $ar = [];
        foreach ($columns as $column) {
            $deatils = explode(',', $column);
            $name = $deatils[0];
            $type = $deatils[1];
            $null = $deatils[2];
            if (Str::startsWith($type, 'entity:')) {
                $type = 'string';
            }
            $ar[] = [
                'name' => str_replace('\r', '', $name),
                'type' => str_replace('\r', '', $type),
                'nullable' => trim(strtolower($null)),
            ];
        }

        if (Schema::hasTable($entity->db_table_name)) {
            Schema::table($entity->db_table_name, function ($table) use ($ar, $entity) {
                foreach ($ar as $column) {
                    $name = $column['name'];
                    $type = $column['type'];
                    $nullable = $column['nullable'] == 'yes' ? true : false;

                    if (Schema::hasColumn($entity->db_table_name, $name)) {
                        $table->$type($name)->nullable($nullable)->change();
                        echo "Column $name updated successfully. <br>";
                    } else {
                        $table->$type($name)->nullable($nullable);
                    }
                }

                // ستون created_by
                if (Schema::hasColumn($entity->db_table_name, 'created_by')) {
                    $table->string('created_by')->nullable(false)->change();
                } else {
                    $table->string('created_by')->nullable(false);
                }

                // ستون updated_by
                if (Schema::hasColumn($entity->db_table_name, 'updated_by')) {
                    $table->string('updated_by')->nullable(false)->change();
                } else {
                    $table->string('updated_by')->nullable(false);
                }

                // ستون contributers
                if (Schema::hasColumn($entity->db_table_name, 'contributers')) {
                    $table->string('contributers')->nullable(false)->change();
                } else {
                    $table->string('contributers')->nullable(false);
                }
            });

            echo "Table $entity->name updated successfully.";
        } else {
            Schema::create($entity->db_table_name, function ($table) use ($ar) {
                $table->string('id', 10)->primary();
                foreach ($ar as $column) {
                    $name = $column['name'];
                    $type = $column['type'];
                    $nullable = $column['nullable'] == 'yes' ? true : false;
                    $table->$type($name)->nullable($nullable);
                }
                $table->string('created_by')->nullable(false);
                $table->string('updated_by')->nullable(false);
                $table->string('contributers')->nullable(false);
                $table->timestamps();
                $table->softDeletes();
            });
            echo "Table $entity->name created successfully.";
        }
        $entitypath = __DIR__ . '/../../Models/Entities';
        if (!file_exists($entitypath)) {
            mkdir($entitypath, 0777, true);
        }
        $entityFile = __DIR__ . '/../../Models/Entities/' . ucfirst($entity->name) . '.php';
        $entity->namespace = "Behin\\SimpleWorkflow\\Models\\Entities";
        $entity->model_name = ucfirst($entity->name);
        $entity->save();
        if (file_exists($entityFile)) {
            unlink($entityFile);
        }
        $entityFileContent = "<?php \n";
        $entityFileContent .= "namespace " . $entity->namespace . "; \n";
        $entityFileContent .= $entity->uses;
        $entityFileContent .= "\n class " . $entity->model_name . " extends Model \n";
        $entityFileContent .= "{ \n";
        $entityFileContent .= "    use SoftDeletes; \n";
        $entityFileContent .= "    public \$incrementing = false; \n";
        $entityFileContent .= "    protected \$keyType = 'string'; \n";
        $entityFileContent .= "    public \$table = '" . $entity->db_table_name . "'; \n";
        $entityFileContent .= "    protected \$fillable = [";
        foreach ($ar as $column) {
            $entityFileContent .= "'" . str_replace('\r', '', $column['name']) . "', ";
        }
        $entityFileContent .= " 'created_by', 'updated_by', 'contributers', ";
        $entityFileContent .= "]; \n";

        $entityFileContent .= "protected static function boot()\n        {\n            parent::boot();\n\n            static::creating(function (\\$model) {\n                \\$model->id = \\$model->id ?? substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'), 0, 10);\n            });\n        }\n";
        $entityFileContent .= $entity->class_contents;
        $entityFileContent .= "}";
        file_put_contents($entityFile, $entityFileContent);
        echo "Entity class " . ucfirst($entity->name) . " created successfully.";
        return redirect()->back();
    }
}
