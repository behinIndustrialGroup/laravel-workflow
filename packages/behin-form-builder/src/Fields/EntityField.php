<?php

namespace MyFormBuilder\Fields;

use Illuminate\Support\Facades\DB;

class EntityField extends AbstractField
{
    public function render(): string
    {
        $id = $this->attributes['id'] ?? '';
        $columns = $this->attributes['columns'] ?? ''; // ['id', 'name' ect. 0 => 'id', 1 => 'name' ect.
        $columns = str_replace("\r", "", $columns);
        $columns = explode("\n", $columns);
        $s = '<div class="form-group table-responsive"';
        if(isset($this->attributes['style'])){
            $s .= " style='" . $this->attributes['style'] . "' ";
        }
        $s .= '>';
        $s .= '<label>';
        $s .= trans('fields.' . $this->name);
        $s .= '</label>';
        $s .= '<table class="table table-bordered" id="'.$id.'">';
        $s .= '<thead>';
        $s.= '<tr>';
        foreach ($columns as $column) {
            $s.= '<th>'.trans('fields.'. $column).'</th>';
        }
        $s .= '</tr>';
        $s .= '</thead>';
        $s .= '<tbody></tbody>';
        $query = $this->attributes['query'] ?? null;
        if($query){
            $rows = DB::select($query);

            foreach ($rows as $row){
                $s .= '<tr>';
                foreach($columns as $column){
                    $s .= '<td>'. $row->$column .'</td>';
                }
                $s .= '</tr>';
            }
        }
        $s .= '</table>';
        if(isset($this->attributes['script'])){
            $s .= '<script>';
            $s .= $this->attributes['script'];
            $s .= '</script>';
        }
        $s .= '</div>';
        return $s;
    }
}
