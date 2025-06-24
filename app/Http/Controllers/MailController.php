<?php

namespace App\Http\Controllers;

use App\Http\Middleware\ApiAuthenticate;
use App\Http\Requests\SendMailRequest;
use App\Models\Mail;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class MailController extends Controller implements HasMiddleware
{
    private function extractFromModel(Mail $draft): array
    {
        return [
            'emailId' => $draft->id,
            'assunto' => $draft->subject ?? "",
            'emailRemetente' => $draft->sender,
            'emailDestinatario' => $draft->recipient ?? "",
            'corpo' => $draft->body ?? "",
            'status' => $draft->status === "sent" ? "enviado" : "lido",
            'dataEnvio' => $draft->sent_at->format('d-m-Y'),
        ];
    }

    public function list()
    {
        $drafts = Mail::where(function (Builder $query) {
            $query->where('recipient', Auth::user()->email);
            // Add orWhere with 'sender' to get sent mails as well
        })->where(function (Builder $query) {
            $query->where('status', 'sent')->orWhere('status', 'read');
        })
            ->orderBy('sent_at', 'desc')
            ->get();

        $formattedDrafts = $drafts->map(fn($draft) => $this->extractFromModel($draft));

        return response()->json([
            'mensagem' => 'Emails encontrados com sucesso',
            'emails' => $formattedDrafts,
        ], 200);
    }

    public function send(SendMailRequest $request)
    {
        $validated = $request->validated();

        if (User::where('email', $validated['emailDestinatario'])->doesntExist()) {
            return response()->json([
                'mensagem' => 'Erro na requisição',
                'erro' => 'Destinatário não encontrado',
            ], 404);
        }

        $mail = Mail::create([
            'subject' => $validated['assunto'],
            'sender' => Auth::user()->email,
            'recipient' => $validated['emailDestinatario'],
            'body' => $validated['corpo'],
            'sent_at' => now(),
            'status' => 'sent',
        ]);

        if ($mail) {
            return response()->json([
                'mensagem' => 'Email enviado com sucesso',
                'email' => $this->extractFromModel($mail),
            ], 200);
        }


        return response()->json([
            'mensagem' => 'Erro interno do servidor',
            'erro' => 'Não foi possível criar o rascunho',
        ], 500);
    }

    public function sendFromDraft(string $draftId)
    {
        $draft = Mail::find($draftId);

        if ($draft && $draft->status === 'draft') {
            if ($draft->sender !== Auth::user()->email) {
                return response()->json([
                    'mensagem' => 'Acesso não autorizado',
                ], 403);
            }

            $validator = Validator::make(
                [
                    'assunto' => $draft->subject,
                    'emailDestinatario' => $draft->recipient,
                    'corpo' => $draft->body,
                ],
                [
                    'assunto' => 'required|max:255',
                    'emailDestinatario' => 'required|email:rfc,dns',
                    'corpo' => 'required|max:10000'
                ],
            );

            if ($validator->fails()) {
                return response()->json([
                    'mensagem' => 'Erro na requisição',
                    'erro' => 'O rascunho não possui todos os campos obrigatórios preenchidos corretamente',
                ], 400);
            }

            if (User::where('email', $draft->recipient)->doesntExist()) {
                return response()->json([
                    'mensagem' => 'Erro na requisição',
                    'erro' => 'Destinatário não encontrado',
                ], 404);
            }

            $draft->status = 'sent';
            $draft->sent_at = now();
            $draft->save();

            return response()->json([
                'mensagem' => 'Email enviado com sucesso',
                'email' => $this->extractFromModel($draft),
            ], 200);
        }

        return response()->json([
            'mensagem' => 'Rascunho não encontrado',
        ], 404);
    }

    public function read(string $draftId)
    {
        $mail = Mail::find($draftId);

        if ($mail && ($mail->status === 'sent' || $mail->status === 'read')) {
            if ($mail->recipient !== Auth::user()->email) {
                return response()->json([
                    'mensagem' => 'Acesso não autorizado',
                ], 403);
            }

            if ($mail->status === 'sent') {
                $mail->status = 'read';
                $mail->save();
            }

            return response()->json([
                'mensagem' => 'Email marcado como lido',
                'email' => $this->extractFromModel($mail),
            ], 200);
        }

        return response()->json([
            'mensagem' => 'Email não encontrado',
        ], 404);
    }

    public static function middleware(): array
    {
        return [
            new Middleware(ApiAuthenticate::class),
        ];
    }
}
