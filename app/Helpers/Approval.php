<?php

use App\Models\Approval_flow;
use App\Models\Approval_process;
use App\Models\Approval_status;
use App\Models\Approval_step;
use App\Models\Proforma_invoice;
use App\Models\Purchase_order;
use App\Models\Purchase_order_payment;
use App\Models\Purchase_requisition;
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
    function createApprovalProcess(string $approval_flow_id, string $approvable_id): void
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
    }
}


/**
 * buat lanjut ke step berikutnya
 */
if (! function_exists('approve')) {
    function approve(Approval_process $approval_process): void
    {
        $approval_process->action = 'Approved';
        $approval_process->save();
    }
}

/**
 * buat ngereject approval
 */
if (! function_exists('rejected')) {
    function rejected(Approval_process $approval_process): void
    {
        $approval_process->action = 'Rejected';
        $approval_process->save();
        done($approval_process->approval_flow_id, $approval_process->approvable_id, 'Rejected');
    }
}

/**
 * buat lanjut ke step berikutnya
 */
if (! function_exists('nextStep')) {
    function nextStep(Approval_process $approval_process): void
    {
        $order = Approval_step::whereKey($approval_process->approval_step_id)
            ->value('order');
        if ($order === null) {
            done(
                $approval_process->approval_flow_id,
                $approval_process->approvable_id,
                'Approved'
            );
            return;
        }

        $nextProcess = Approval_process::where('approval_flow_id', $approval_process->approval_flow_id)
            ->whereHas('approval_step', function ($query) use ($order) {
                $query->where('order', $order + 1);
            })
            ->first();

        if ($nextProcess) {
            $nextProcess->update([
                'action' => 'Open',
            ]);
            Approval_status::where('approval_flow_id', $approval_process->approval_flow_id)
                ->where('approvable_id', $approval_process->approvable_id)
                ->update([
                    'step' => $order + 1,
                ]);
        } else {
            done(
                $approval_process->approval_flow_id,
                $approval_process->approvable_id,
                'Approved'
            );
        }
    }
}

/**
 * ini buat nyelesaiin approvalnya.
 * approval terakhir
 */
if (! function_exists('done')) {
    function done(string $approval_flow_id, string $approvable_id, string $status): void
    {
        Approval_status::where('approval_flow_id', $approval_flow_id)
            ->where('approvable_id', $approvable_id)
            ->update(['status' => 'Done']);
        $approval_flow = Approval_flow::find($approval_flow_id);
        $models = [
            'Purchase_requisition' => Purchase_requisition::class,
            'Purchase_order' => Purchase_order::class,
            'Proforma_invoice' => Proforma_invoice::class,
            'Purchase_order_payment' => Purchase_order_payment::class,
        ];
        $model = $models[class_basename($approval_flow?->approvable_model)] ?? null;
        if ($model) {
            $model::whereKey($approvable_id)->update([
                'status' => $status,
            ]);
        }
    }
}
