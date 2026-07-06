<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PosTerminal;
use App\Models\PrintJob;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PrinterBridgeController extends Controller
{
    public function me(Request $request): JsonResponse
    {
        $terminal = $request->attributes->get('terminal');

        return response()->json([
            'success' => true,
            'message' => 'Terminal valid.',
            'terminal' => [
                'id' => $terminal->id,
                'kode_terminal' => $terminal->kode_terminal,
                'nama_terminal' => $terminal->nama_terminal,
                'status' => $terminal->status ?? null,
            ],
        ]);
    }

    public function heartbeat(Request $request): JsonResponse
    {
        $terminal = $request->attributes->get('terminal');

        $data = $request->validate([
            'device_name' => ['nullable', 'string', 'max:255'],
            'app_version' => ['nullable', 'string', 'max:50'],
        ]);

        $updates = [];

        if (Schema::hasColumn('pos_terminals', 'last_seen_at')) {
            $updates['last_seen_at'] = now();
        }

        if (Schema::hasColumn('pos_terminals', 'bridge_last_seen_at')) {
            $updates['bridge_last_seen_at'] = now();
        }

        if (Schema::hasColumn('pos_terminals', 'bridge_device_name')) {
            $updates['bridge_device_name'] = $data['device_name'] ?? null;
        }

        if (Schema::hasColumn('pos_terminals', 'bridge_app_version')) {
            $updates['bridge_app_version'] = $data['app_version'] ?? null;
        }

        if (! empty($updates)) {
            $terminal->update($updates);
        }

        return response()->json([
            'success' => true,
            'message' => 'Heartbeat diterima.',
            'server_time' => now()->toDateTimeString(),
        ]);
    }

    public function nextPrintJob(Request $request): JsonResponse
    {
        $terminal = $request->attributes->get('terminal');

        $job = DB::transaction(function () use ($terminal) {
            $staleLimit = now()->subSeconds(5);

            PrintJob::query()
                ->where('pos_terminal_id', $terminal->id)
                ->where('status', 'printing')
                ->where(function ($query) use ($staleLimit) {
                    $query->whereNull('locked_at')
                        ->orWhere('locked_at', '<=', $staleLimit);
                })
                ->where('attempts', '<', 10)
                ->update([
                    'status' => 'pending',
                    'locked_at' => null,
                    'error_message' => 'Dikembalikan ke pending karena proses cetak timeout.',
                ]);

            PrintJob::query()
                ->where('pos_terminal_id', $terminal->id)
                ->where('status', 'printing')
                ->where(function ($query) use ($staleLimit) {
                    $query->whereNull('locked_at')
                        ->orWhere('locked_at', '<=', $staleLimit);
                })
                ->where('attempts', '>=', 10)
                ->update([
                    'status' => 'failed',
                    'failed_at' => now(),
                    'error_message' => 'Print job gagal setelah beberapa kali percobaan.',
                ]);

            $job = PrintJob::query()
                ->where('pos_terminal_id', $terminal->id)
                ->where('status', 'pending')
                ->orderBy('created_at')
                ->lockForUpdate()
                ->first();

            if (! $job) {
                return null;
            }

            $job->update([
                'status' => 'printing',
                'attempts' => ((int) $job->attempts) + 1,
                'locked_at' => now(),
                'error_message' => null,
            ]);

            return $job->fresh();
        });

        if (! $job) {
            return response()->json([
                'success' => true,
                'has_job' => false,
                'message' => 'Tidak ada antrean cetak.',
                'job' => null,
            ]);
        }

        return response()->json([
            'success' => true,
            'has_job' => true,
            'message' => 'Ada antrean cetak.',
            'job' => $this->formatPrintJob($job),
        ]);
    }

    public function markPrinted(Request $request, PrintJob $printJob): JsonResponse
    {
        $terminal = $request->attributes->get('terminal');

        $this->ensureTerminalOwnsJob($terminal->id, $printJob);

        $printJob->update([
            'status' => 'printed',
            'printed_at' => now(),
            'failed_at' => null,
            'error_message' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Print job ditandai berhasil dicetak.',
            'job' => $this->formatPrintJob($printJob->fresh()),
        ]);
    }



    public function markFailed(Request $request, PrintJob $printJob): JsonResponse
    {
        $terminal = $request->attributes->get('terminal');

        $this->ensureTerminalOwnsJob($terminal->id, $printJob);

        $data = $request->validate([
            'error_message' => ['required', 'string', 'max:1000'],
        ]);

        $printJob->update([
            'status' => 'failed',
            'failed_at' => now(),
            'error_message' => $data['error_message'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Print job ditandai gagal.',
            'job' => $this->formatPrintJob($printJob->fresh()),
        ]);
    }

    protected function ensureTerminalOwnsJob(int $terminalId, PrintJob $printJob): void
    {
        abort_if((int) $printJob->pos_terminal_id !== $terminalId, 404);
    }

    protected function formatPrintJob(PrintJob $job): array
    {
        return [
            'id' => $job->id,
            'type' => $job->type,
            'status' => $job->status,
            'attempts' => (int) $job->attempts,
            'payload' => $job->payload,
            'created_at' => $job->created_at?->toDateTimeString(),
            'locked_at' => $job->locked_at?->toDateTimeString(),
            'printed_at' => $job->printed_at?->toDateTimeString(),
            'failed_at' => $job->failed_at?->toDateTimeString(),
            'error_message' => $job->error_message,
        ];
    }
}