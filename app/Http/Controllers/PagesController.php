<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Page;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PagesController extends Controller
{
    public function show(Page $page): JsonResponse
    {
        return response()->json([
            'id'       => $page->id,
            'numero'   => $page->numero,
            'titulo'   => $page->titulo,
            'conteudo' => $page->conteudo ?? '',
        ]);
    }

    public function store(Request $request, Book $book): JsonResponse
    {
        $data = $request->validate([
            'titulo'   => 'required|string|max:255',
            'conteudo' => 'nullable|string',
        ]);

        $count = $book->pages()->count();

        $page = Page::create([
            'book_id'  => $book->id,
            'numero'   => $count + 1,
            'titulo'   => $data['titulo'],
            'conteudo' => $data['conteudo'] ?? null,
            'ordem'    => $count + 1,
        ]);

        return response()->json([
            'id'       => $page->id,
            'numero'   => $page->numero,
            'titulo'   => $page->titulo,
            'conteudo' => $page->conteudo ?? '',
        ], 201);
    }

    public function update(Request $request, Page $page): JsonResponse
    {
        $data = $request->validate([
            'titulo'   => 'sometimes|required|string|max:255',
            'conteudo' => 'nullable|string',
        ]);

        $page->update($data);

        return response()->json(['ok' => true]);
    }

    public function destroy(Page $page): JsonResponse
    {
        $page->delete();

        return response()->json(['ok' => true]);
    }
}
