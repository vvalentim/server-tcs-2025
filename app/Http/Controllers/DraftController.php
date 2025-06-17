<?php

namespace App\Http\Controllers;

use App\Http\Middleware\ApiAuthenticate;
use App\Http\Requests\StoreDraftRequest;
use App\Models\Mail;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;

class DraftController extends Controller implements HasMiddleware
{
    private function extractFromModel(Mail $draft): array
    {
        return [
            'rascunhoId' => $draft->id,
            'assunto' => $draft->subject ?? "",
            'emailDestinatario' => $draft->recipient ?? "",
            'corpo' => $draft->body ?? "",
        ];
    }

    public function list()
    {
        $drafts = Mail::where('status', 'draft')
            ->where('sender', Auth::user()->email)
            ->orderBy('updated_at', 'desc')
            ->get();

        $formattedDrafts = $drafts->map(fn($draft) => $this->extractFromModel($draft));

        return response()->json([
            'mensagem' => 'Rascunhos encontrados com sucesso',
            'rascunhos' => $formattedDrafts,
        ], 200);
    }

    public function store(StoreDraftRequest $request)
    {
        $validated = $request->validated();

        $draft = Mail::create([
            'subject' => $validated['assunto'] ?? null,
            'sender' => Auth::user()->email,
            'recipient' => $validated['emailDestinatario'] ?? null,
            'body' => $validated['corpo'] ?? null,
            'status' => 'draft',
        ]);

        if ($draft) {
            return response()->json([
                'mensagem' => 'Rascunho criado com sucesso',
                'rascunho' => $this->extractFromModel($draft),
            ], 201);
        }

        return response()->json([
            'mensagem' => 'Erro interno do servidor',
            'erro' => 'Não foi possível criar o rascunho',
        ], 500);
    }

    public function show(string $draftId)
    {
        $draft = Mail::find($draftId);

        if ($draft && $draft->status === 'draft') {
            if ($draft->sender !== Auth::user()->email) {
                return response()->json([
                    'mensagem' => 'Acesso não autorizado',
                ], 403);
            }

            return response()->json([
                'mensagem' => 'Sucesso ao buscar rascunho',
                'rascunho' => $this->extractFromModel($draft),
            ], 200);
        }

        return response()->json([
            'mensagem' => 'Rascunho não encontrado',
        ], 404);
    }

    public function edit(StoreDraftRequest $request, string $draftId)
    {
        $validated = $request->validated();
        $draft = Mail::find($draftId);

        if (!$draft || $draft->status !== 'draft') {
            return response()->json([
                'mensagem' => 'Rascunho não encontrado',
            ], 404);
        }

        if ($draft->sender !== Auth::user()->email) {
            return response()->json([
                'mensagem' => 'Acesso não autorizado',
            ], 403);
        }

        $draft->update([
            'subject' => $validated['assunto'] ?? null,
            'recipient' => $validated['emailDestinatario'] ?? null,
            'body' => $validated['corpo'] ?? null,
            'status' => 'draft',
        ]);

        return response()->json([
            'mensagem' => 'Rascunho atualizado com sucesso',
            'rascunho' => $this->extractFromModel($draft),
        ], 200);
    }

    public function destroy(string $draftId)
    {
        $draft = Mail::find($draftId);

        if (!$draft || $draft->status !== 'draft') {
            return response()->json([
                'mensagem' => 'Rascunho não encontrado',
            ], 404);
        }

        if ($draft->sender !== Auth::user()->email) {
            return response()->json([
                'mensagem' => 'Acesso não autorizado',
            ], 403);
        }

        $draft->delete();

        return response()->json([
            'mensagem' => 'Rascunho excluído com sucesso',
        ], 200);
    }

    public static function middleware(): array
    {
        return [
            new Middleware(ApiAuthenticate::class),
        ];
    }
}
