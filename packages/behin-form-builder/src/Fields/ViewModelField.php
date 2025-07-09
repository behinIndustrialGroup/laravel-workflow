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
        $s .= "<button type='button' class='btn btn-sm ' onclick='get_view_model_rows(\"$viewModel->id\", \"$viewModel->api_key\")'>";
        $s .= "<i class='fa fa-refresh'></i> ";
        $s .= "</button>";
        $s .= "<h5 class='mb-0'>" . trans('fields.' . $viewModel->name) . "</h5>";

        $s .= "</div>";

        $s .= "<table class='table table-striped' id='{$viewModel->id}'>";
        $s .= "<thead><tr>";
        foreach ($columns as $column) {
            $columnLabel = trans("fields." . $column);
            $s .= "<th>$columnLabel</th>";
        }
        $s .= "<th></th>";
        $s .= "</tr></thead>";

        $s .= "<tbody></tbody>";

        if ($viewModel->allow_create_row) {
            $s .= "<tfoot><tr>";
            $colspan = count($columns) +1;
            $btnLabel = trans('fields.Create new');
            $s .= "<td colspan='{$colspan}'>";
            $s .= "<button class='btn btn-sm btn-primary' onclick='open_view_model_create_new_form(`$viewModel->create_form`, `$viewModel->id`, `$viewModel->api_key`)'>";
            $s .= "{$btnLabel}</button></td>";
            $s .= "</tr></tfoot>";
        }

        $s .= "</table>";
        $s .= "</div>";

        $s .= "<script>get_view_model_rows(`$viewModel->id`, `$viewModel->api_key`)</script>";

        return $s;
    }
}
