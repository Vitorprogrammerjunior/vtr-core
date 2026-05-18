<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BooksController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate(['titulo' => 'required|string|max:255']);

        $ordem = Book::max('ordem') ?? 0;

        $book = Book::create([
            'titulo' => $data['titulo'],
            'status' => 'em_andamento',
            'ordem'  => $ordem + 1,
        ]);

        return response()->json([
            'id'            => $book->id,
            'titulo'        => $book->titulo,
            'paginas_total' => 0,
            'status'        => $book->status,
            'status_label'  => 'EM ANDAMENTO',
            'criado_em'     => $book->created_at->format('d/m/Y'),
            'ultima_edicao' => 'HOJE ' . $book->created_at->format('H:i'),
        ], 201);
    }

    public function update(Request $request, Book $book): JsonResponse
    {
        $data = $request->validate([
            'titulo' => 'sometimes|required|string|max:255',
            'status' => 'sometimes|required|in:em_andamento,pausado,concluido',
        ]);

        $book->update($data);

        $statusLabel = match ($book->status) {
            'em_andamento' => 'EM ANDAMENTO',
            'pausado'      => 'PAUSADO',
            'concluido'    => 'CONCLUÍDO',
            default        => mb_strtoupper($book->status),
        };

        return response()->json([
            'titulo'        => $book->titulo,
            'status'        => $book->status,
            'status_label'  => $statusLabel,
            'ultima_edicao' => 'HOJE ' . $book->updated_at->format('H:i'),
        ]);
    }

    public function destroy(Book $book): JsonResponse
    {
        $book->delete();

        return response()->json(['ok' => true]);
    }

    public function pages(Book $book): JsonResponse
    {
        $pagesDb = $book->pages()->orderBy('ordem')->get();
        $first   = $pagesDb->first();

        return response()->json([
            'pages' => $pagesDb->map(fn($p) => [
                'id'     => $p->id,
                'numero' => $p->numero,
                'titulo' => $p->titulo,
            ])->values()->all(),
            'activePage' => $first ? [
                'id'       => $first->id,
                'numero'   => $first->numero,
                'titulo'   => $first->titulo,
                'conteudo' => $first->conteudo ?? '',
            ] : null,
        ]);
    }
}
