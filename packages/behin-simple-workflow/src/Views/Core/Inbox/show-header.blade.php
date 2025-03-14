<div class="card shadow-sm mb-4 bg-dark">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">{{ $task->name ?? '' }} - {{ $inbox->case_name ?? '' }}</h6>
    </div>
    <div class="card-body">
        <p class="mb-0">
            {{ trans('fields.Case Number') }}: <span class="badge badge-secondary">{{ $case->number }}</span> <br>
            {{ trans('fields.Creator') }}: <span class="badge badge-light">{{ getUserInfo($case->creator)->name }}</span>
            <br>
            {{ trans('fields.Created At') }}: <span class="badge badge-light"
                dir="ltr">{{ toJalali($case->created_at)->format('Y-m-d H:i') }}</span>
            <br>
            <span class="badge badge-light" style="color: dark">{{ $case->id }}</span>
        </p>
    </div>
</div>
