<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PosTerminal\StorePosTerminalRequest;
use App\Http\Requests\Admin\PosTerminal\UpdatePosTerminalRequest;
use App\Models\PosTerminal;
use App\Services\Admin\PosTerminalService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PosTerminalController extends Controller
{
    public function __construct(
        protected PosTerminalService $posTerminalService
    ) {}

    public function index(Request $request): View
    {
        return view('admin.pos-terminals.index', [
            'terminals' => $this->posTerminalService->getPaginatedTerminals($request),
            'statuses' => $this->posTerminalService->statuses(),
        ]);
    }

    public function create(): View
    {
        return view('admin.pos-terminals.create');
    }

    public function store(StorePosTerminalRequest $request): RedirectResponse
    {
        $terminal = $this->posTerminalService->createTerminal($request->validated());

        return redirect()
            ->route('admin.pos-terminals.show', $terminal)
            ->with('success', 'Terminal kasir berhasil ditambahkan. Token bridge otomatis dibuat.');
    }

    public function show(PosTerminal $posTerminal): View
    {
        return view('admin.pos-terminals.show', [
            'terminal' => $posTerminal,
        ]);
    }

    public function edit(PosTerminal $posTerminal): View
    {
        return view('admin.pos-terminals.edit', [
            'terminal' => $posTerminal,
            'statuses' => $this->posTerminalService->statuses(),
        ]);
    }

    public function update(UpdatePosTerminalRequest $request, PosTerminal $posTerminal): RedirectResponse
    {
        $this->posTerminalService->updateTerminal($posTerminal, $request->validated());

        return redirect()
            ->route('admin.pos-terminals.show', $posTerminal)
            ->with('success', 'Terminal kasir berhasil diperbarui.');
    }

    public function updateStatus(PosTerminal $posTerminal): RedirectResponse
    {
        $posTerminal = $this->posTerminalService->toggleStatus($posTerminal);

        $message = $posTerminal->status === 'aktif'
            ? 'Terminal kasir berhasil diaktifkan.'
            : 'Terminal kasir berhasil dinonaktifkan.';

        return redirect()
            ->back()
            ->with('success', $message);
    }

    public function regenerateToken(PosTerminal $posTerminal): RedirectResponse
    {
        $this->posTerminalService->regenerateToken($posTerminal);

        return redirect()
            ->back()
            ->with('success', 'Token bridge terminal berhasil dibuat ulang. Flutter Bridge harus menggunakan token baru.');
    }

    public function destroy(PosTerminal $posTerminal): RedirectResponse
    {
        try {
            $this->posTerminalService->deleteTerminal($posTerminal);

            return redirect()
                ->route('admin.pos-terminals.index')
                ->with('success', 'Terminal kasir berhasil dihapus.');
        } catch (Exception $exception) {
            return redirect()
                ->back()
                ->with('error', $exception->getMessage());
        }
    }
}