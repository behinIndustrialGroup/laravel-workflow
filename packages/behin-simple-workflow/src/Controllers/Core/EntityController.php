<?php

namespace Behin\SimpleWorkflow\Controllers\Core;

use App\Http\Controllers\Controller;
use Behin\SimpleWorkflow\Models\Core\Entity;
use Behin\SimpleWorkflow\Models\Core\Fields;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

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
        return view('SimpleWorkflowView::Core.Entity.edit', compact('entity'));
    }

    public function update(Request $request, Entity $entity)
    {
        $entity->update([
            'name' => $request->name,
            'description' => $request->description,
            'columns' => $request->columns,
            'uses' => $request->uses,
            'class_contents' => $request->class_contents,
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
            $ar[] = [
                'name' => str_replace('\r', '', $name),
                'type' => str_replace('\r', '', $type),
                'nullable' => trim(strtolower($null)),
            ];
            // $column['name'] = $deatils[0];
        }

        if (Schema::hasTable($entity->db_table_name)) {
            Schema::table($entity->db_table_name, function ($table) use ($ar, $entity) {
                foreach ($ar as $column) {
                    $name = $column['name'];
                    $type = $column['type'];
                    $nullable = $column['nullable'] == 'yes' ? true : false;
                    // $table->$type($name)->nullable($nullable)->change();
                    if (Schema::hasColumn($entity->db_table_name, $name)) {
                        $table->$type($name)->nullable($nullable)->change();
                        echo "Column $name updated successfully. <br>";
                    } else {
                        $table->$type($name)->nullable($nullable);
                    }
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
        $entity->namespace = "Behin\SimpleWorkflow\Models\Entities";
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
        $entityFileContent .= "]; \n";

        $entityFileContent .= "protected static function boot()
        {
            parent::boot();

            static::creating(function (\$model) {
                \$model->id = \$model->id ?? substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'), 0, 10);
            });
        }\n";
        $entityFileContent .= $entity->class_contents;
        $entityFileContent .= "}";
        file_put_contents($entityFile, $entityFileContent);
        echo "Entity class " . ucfirst($entity->name) . " created successfully.";
    }
}
