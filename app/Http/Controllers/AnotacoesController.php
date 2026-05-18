<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

class AnotacoesController extends Controller
{
    public function index(Request $request)
    {
        $user    = $request->user();
        $profile = $user->profile;

        $booksDb = Book::withCount('pages')->orderBy('ordem')->get();

        $statusLabel = fn(string $s) => match ($s) {
            'em_andamento' => 'EM ANDAMENTO',
            'pausado'      => 'PAUSADO',
            'concluido'    => 'CONCLUÍDO',
            default        => mb_strtoupper($s),
        };

        $books = $booksDb->map(fn(Book $b) => [
            'id'            => $b->id,
            'titulo'        => $b->titulo,
            'paginas_total' => $b->pages_count,
            'status'        => $b->status,
            'status_label'  => $statusLabel($b->status),
            'criado_em'     => $b->created_at?->format('d/m/Y') ?? '—',
            'ultima_edicao' => $b->updated_at?->isToday()
                ? 'HOJE ' . $b->updated_at->format('H:i')
                : ($b->updated_at?->format('d/m/Y H:i') ?? '—'),
        ])->all();

        $activeBookModel = $booksDb->first();
        $activeBook      = $books[0] ?? null;

        $pagesDb = $activeBookModel
            ? $activeBookModel->pages()->orderBy('ordem')->get()
            : collect();

        $pages = $pagesDb->map(fn($p) => [
            'id'     => $p->id,
            'numero' => $p->numero,
            'titulo' => $p->titulo,
        ])->all();

        $firstPage  = $pagesDb->first();
        $activePage = $firstPage ? [
            'id'       => $firstPage->id,
            'numero'   => $firstPage->numero,
            'titulo'   => $firstPage->titulo,
            'conteudo' => $firstPage->conteudo ?? '',
        ] : null;

        return view('anotacoes', compact('user', 'profile', 'books', 'activeBook', 'pages', 'activePage'));
    }
}
