<?php

namespace MyFormBuilder\Fields;

use Behin\SimpleWorkflow\Controllers\Core\ViewModelController;
use Behin\SimpleWorkflow\Models\Core\Entity;
use Behin\SimpleWorkflow\Models\Core\ViewModel;

class ViewModelField extends AbstractField
{
    public function render(): string
    {
        $id = $this->attributes['id'];
        $viewModelId = $this->attributes['view_model_id'];
        $style = $this->attributes['style'] ?? '';
        $s = "";

        $viewModel = ViewModel::find($viewModelId);
        $model = ViewModelController::getModelById($viewModelId);
        $columns = explode(',', $viewModel->default_fields);
        $max_number_of_rows = $viewModel->max_number_of_rows;

        $s .= "<div class='table-responsive card p-1' style='" . $style . "'>";

        // ✅ اضافه کردن دکمه رفرش بالا
        $s .= "<div class='d-flex align-items-center mb-2'>";
        $s .= "<button type='button' class='btn btn-sm material-icons' onclick='get_view_model_rows(\"$viewModel->id\", \"$viewModel->api_key\")'>";
        $s .= "<i class='fa fa-refresh'></i>";
        $s .= "</button>";
        $s .= "<h5 class='mb-0'>" . trans('fields.' . $viewModel->name) . "</h5>";

        $s .= "</div>";

        $s .= "<table class='table table-striped' id='{$viewModel->id}' style='width: 100%'>";
        if ($viewModel->show_as == 'table') {

            $s .= "<thead><tr>";
            foreach ($columns as $column) {
                $columnLabel = trans("fields." . $column);
                $s .= "<th>$columnLabel</th>";
            }
            $s .= "<th></th>";
            $s .= "</tr></thead>";
        }


        $s .= "<tbody></tbody>";



        $s .= "</table>";
        $s .= "</div>";

        $s .= "<script>get_view_model_rows(`$viewModel->id`, `$viewModel->api_key`)</script>";

        return $s;
    }
}
