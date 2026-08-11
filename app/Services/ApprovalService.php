<?php

namespace App\Services;

use App\Models\Approval_flow;
use App\Models\Approval_process;
use App\Models\Approval_status;
use App\Models\Approval_step;
use App\Models\Invoice;
use App\Models\Invoice_payment;
use App\Models\Proforma_invoice;
use App\Models\Purchase_order;
use App\Models\Purchase_order_payment;
use App\Models\Purchase_requisition;
use Illuminate\Support\Str;
use RuntimeException;

class ApprovalService
{
    /**
     * Mengecek apakah model pada department tertentu
     * mempunyai approval flow.
     */
    public function checkHasApproval(
        string $model,
        string $department
    ): bool {
        return Approval_flow::where('approvable_model', $model)
            ->where('department', $department)
            ->exists();
    }

    /**
     * Mengambil ID approval flow berdasarkan model dan department.
     */
    public function getApprovalFlowId(
        string $model,
        string $department
    ): string {
        $approvalFlow = Approval_flow::where('approvable_model', $model)
            ->where('department', $department)
            ->first();

        if (!$approvalFlow) {
            throw new RuntimeException(
                "Approval flow untuk model {$model} dan department {$department} tidak ditemukan."
            );
        }

        return $approvalFlow->id;
    }

    /**
     * Membuat seluruh approval process berdasarkan approval step.
     */
    public function createApprovalProcess(
        string $approval_flow_id,
        string $approvable_id
    ): void {
        $requestToken = (string) Str::uuid();

        $approvalSteps = Approval_step::where(
            'approval_flow_id',
            $approval_flow_id
        )
            ->orderBy('order')
            ->get();

        if ($approvalSteps->isEmpty()) {
            throw new RuntimeException(
                "Approval step untuk flow {$approval_flow_id} tidak ditemukan."
            );
        }

        Approval_status::create([
            'request_token' => $requestToken,
            'approval_flow_id' => $approval_flow_id,
            'approvable_id' => $approvable_id,
            'step' => 1,
            'status' => 'Open',
        ]);

        foreach ($approvalSteps as $step) {
            Approval_process::create([
                'request_token' => $requestToken,
                'approval_flow_id' => $approval_flow_id,
                'approval_step_id' => $step->id,
                'approvable_id' => $approvable_id,
                'user_id' => $step->user_id,
                'action' => $step->order == 1
                    ? 'Open'
                    : 'Create',
            ]);
        }
    }

    /**
     * Approve proses approval saat ini.
     */
    public function approve(
        Approval_process $approval_process
    ): void {
        $approval_process->update([
            'action' => 'Approved',
        ]);
    }

    /**
     * Reject proses approval dan mengakhiri approval flow.
     */
    public function rejected(
        Approval_process $approval_process
    ): void {
        $approval_process->update([
            'action' => 'Rejected',
        ]);

        $this->done(
            $approval_process->approval_flow_id,
            $approval_process->approvable_id,
            'Rejected'
        );
    }

    /**
     * Membuka approval step berikutnya.
     *
     * Jika tidak ada step berikutnya,
     * approval dianggap selesai.
     */
    public function nextStep(
        Approval_process $approval_process
    ): void {
        $order = Approval_step::whereKey(
            $approval_process->approval_step_id
        )->value('order');

        /*
         * Jika approval step sudah tidak ditemukan,
         * anggap proses approval selesai.
         */
        if ($order === null) {
            $this->done(
                $approval_process->approval_flow_id,
                $approval_process->approvable_id,
                'Approved'
            );

            return;
        }

        /*
         * Cari approval process dengan step berikutnya.
         */
        $nextProcess = Approval_process::where(
            'approval_flow_id',
            $approval_process->approval_flow_id
        )
            ->where(
                'approvable_id',
                $approval_process->approvable_id
            )
            ->whereHas(
                'approval_step',
                function ($query) use (
                    $order,
                    $approval_process
                ) {
                    $query
                        ->where(
                            'approval_flow_id',
                            $approval_process->approval_flow_id
                        )
                        ->where(
                            'order',
                            $order + 1
                        );
                }
            )
            ->first();

        /*
         * Jika masih ada step berikutnya,
         * buka step tersebut.
         */
        if ($nextProcess) {
            $nextProcess->update([
                'action' => 'Open',
            ]);

            Approval_status::where(
                'approval_flow_id',
                $approval_process->approval_flow_id
            )
                ->where(
                    'approvable_id',
                    $approval_process->approvable_id
                )
                ->update([
                    'step' => $order + 1,
                ]);

            return;
        }

        /*
         * Tidak ada step selanjutnya,
         * approval selesai.
         */
        $this->done(
            $approval_process->approval_flow_id,
            $approval_process->approvable_id,
            'Approved'
        );
    }

    /**
     * Menyelesaikan approval flow.
     *
     * Approval_status menjadi Done,
     * sedangkan status document menjadi
     * Approved atau Rejected.
     */
    public function done(
        string $approval_flow_id,
        string $approvable_id,
        string $status
    ): void {
        /*
         * Tandai approval workflow selesai.
         */
        Approval_status::where(
            'approval_flow_id',
            $approval_flow_id
        )
            ->where(
                'approvable_id',
                $approvable_id
            )
            ->update([
                'status' => 'Done',
            ]);

        /*
         * Cari approval flow.
         */
        $approvalFlow = Approval_flow::find(
            $approval_flow_id
        );

        if (!$approvalFlow) {
            throw new RuntimeException(
                "Approval flow {$approval_flow_id} tidak ditemukan."
            );
        }

        /*
         * Mapping approvable model.
         */
        $models = [
            'Purchase_requisition' => Purchase_requisition::class,
            'Purchase_order' => Purchase_order::class,
            'Proforma_invoice' => Proforma_invoice::class,
            'Purchase_order_payment' => Purchase_order_payment::class,
            'Invoice' => Invoice::class,
            'Invoice_payment' => Invoice_payment::class,
        ];

        /*
         * Contoh:
         *
         * App\Models\Purchase_order
         *
         * menjadi:
         *
         * Purchase_order
         */
        $modelName = class_basename(
            $approvalFlow->approvable_model
        );

        $model = $models[$modelName] ?? null;

        if (!$model) {
            throw new RuntimeException(
                "Approvable model {$modelName} tidak terdaftar pada ApprovalService."
            );
        }

        /*
         * Update status document utama.
         */
        $updated = $model::whereKey(
            $approvable_id
        )->update([
            'status' => $status,
        ]);

        if (!$updated) {
            throw new RuntimeException(
                "Data {$modelName} dengan ID {$approvable_id} tidak ditemukan."
            );
        }
    }
}
