<?php

use App\Models\Approval_flow;
use App\Models\Approval_process;
use App\Models\Approval_status;
use App\Models\Approval_step;

/**
 * cek ada ngeset approval gak?
 */
if (! function_exists('checkHasApproval')) {
    function checkHasApproval(string $model): string
    {
        return Approval_flow::where('approvable_model', $model)->exists();
    }
}

/**
 * ambil id approval flow
 */
if (! function_exists('getApprovalFlowId')) {
    function getApprovalFlowId(string $model): string
    {
        $approval_flow = Approval_flow::where('approvable_model', $model)->first();
        return $approval_flow->id;
    }
}

/**
 * membuat proses approval baru
 */
if (! function_exists('createApprovalProcess')) {
    function createApprovalProcess(string $approval_flow_id, string $approvable_id): string
    {
        $approval_step = Approval_step::where('approval_flow_id', $approval_flow_id)->orderBy('order')->first();
        Approval_status::create([
            'approval_flow_id' => $approval_flow_id,
            'approvable_id' => $approvable_id,
            'step' => 1,
            'status' => 'Open'
        ]);
        Approval_process::create([
            'approval_flow_id' => $approval_flow_id,
            'approval_step_id' => $approval_step->id,
            'approvable_id' => $approvable_id,
            'action' => 'Create',

        ]);
    }
}


/**
 * buat lanjut ke step berikutnya
 */
if (! function_exists('nextStep')) {
    function nextStep(string $model, string $id, string $approval_flow_id): string
    {

        return sprintf('%02d:%02d', $hour, $minute);
    }
}

if (! function_exists('nextStep')) {
    function nextStep(string $model, string $id, string $approval_flow_id): string
    {

        return sprintf('%02d:%02d', $hour, $minute);
    }
}

if (! function_exists('doneApproval')) {
    function doneApproval(string $model, string $id, string $approval_flow_id): string
    {

        return sprintf('%02d:%02d', $hour, $minute);
    }
}
