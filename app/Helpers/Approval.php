<?php

use App\Models\Approval_flow;
use App\Models\Approval_process;
use App\Models\Approval_status;
use App\Models\Approval_step;
use Illuminate\Support\Str;

/**
 * cek ada ngeset approval gak?
 */
if (! function_exists('checkHasApproval')) {
    function checkHasApproval(string $model, string $department): string
    {
        return Approval_flow::where('approvable_model', $model)
            ->where('department', $department)
            ->exists();
    }
}

/**
 * ambil id approval flow
 */
if (! function_exists('getApprovalFlowId')) {
    function getApprovalFlowId(string $model, string $department): string
    {
        $approval_flow = Approval_flow::where('approvable_model', $model)
            ->where('department', $department)
            ->first();
        return $approval_flow->id;
    }
}

/**
 * membuat proses approval baru
 */
if (! function_exists('createApprovalProcess')) {
    function createApprovalProcess(string $approval_flow_id, string $approvable_id): string
    {
        $request_token = (string) Str::uuid();
        $approval_step = Approval_step::where('approval_flow_id', $approval_flow_id)->orderBy('order')->get();
        Approval_status::create([
            'request_token' =>  $request_token,
            'approval_flow_id' => $approval_flow_id,
            'approvable_id' => $approvable_id,
            'step' => 1,
            'status' => 'Open'
        ]);
        foreach ($approval_step as $d) {
            Approval_process::create([
                'request_token' => $request_token,
                'approval_flow_id' => $approval_flow_id,
                'approval_step_id' => $d->id,
                'approvable_id' => $approvable_id,
                'user_id' => $d->user_id,
                'action' => $d->order == 1 ? 'Open' : 'Create',
            ]);
        }
        return true;
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

/**
 * buat ngereject approval
 */
if (! function_exists('rejected')) {
    function rejected(string $model, string $id, string $approval_flow_id): string
    {

        return sprintf('%02d:%02d', $hour, $minute);
    }
}

/**
 * ini buat nyelesaiin approvalnya.
 * approval terakhir
 */
if (! function_exists('doneApproval')) {
    function doneApproval(string $model, string $id, string $approval_flow_id): string
    {

        return sprintf('%02d:%02d', $hour, $minute);
    }
}
