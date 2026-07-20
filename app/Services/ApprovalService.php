<?php

namespace App\Services;

use App\Models\Approval_flow;
use App\Models\Approval_process;
use App\Models\Approval_status;
use App\Models\Approval_step;
use App\Models\Proforma_invoice;
use App\Models\Purchase_order;
use App\Models\Purchase_order_payment;
use App\Models\Purchase_requisition;
use Illuminate\Support\Str;

class ApprovalService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    function checkHasApproval(string $model, string $department): string
    {
        return Approval_flow::where('approvable_model', $model)
            ->where('department', $department)
            ->exists();
    }

    function getApprovalFlowId(string $model, string $department): string
    {
        $approval_flow = Approval_flow::where('approvable_model', $model)
            ->where('department', $department)
            ->first();
        return $approval_flow->id;
    }

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

    function approve(Approval_process $approval_process): void
    {
        $approval_process->action = 'Approved';
        $approval_process->save();
    }

    function rejected(Approval_process $approval_process): void
    {
        $approval_process->action = 'Rejected';
        $approval_process->save();
        done($approval_process->approval_flow_id, $approval_process->approvable_id, 'Rejected');
    }

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
            ->where('approvable_id', $approval_process->approvable_id)
            ->whereHas('approval_step', function ($query) use ($order, $approval_process) {
                $query->where('approval_flow_id', $approval_process->approval_flow_id);
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
