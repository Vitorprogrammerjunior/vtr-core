<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ObjetivosController;
use App\Http\Controllers\HabitsController;
use App\Http\Controllers\GoalsController;
use App\Http\Controllers\TreinosController;
use App\Http\Controllers\WorkoutsController;
use App\Http\Controllers\ExercisesController;
use App\Http\Controllers\ExerciseSetsController;
use App\Http\Controllers\AnotacoesController;
use App\Http\Controllers\AlimentacaoController;
use App\Http\Controllers\BooksController;
use App\Http\Controllers\PagesController;
use App\Http\Controllers\MealsController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('dashboard'));

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/objetivos', [ObjetivosController::class, 'index'])->name('objetivos');
    Route::post('/objetivos/habitos', [HabitsController::class, 'store'])->name('habitos.store');
    Route::post('/objetivos/habitos/{habit}/toggle', [HabitsController::class, 'toggle'])->name('habitos.toggle');
    Route::delete('/objetivos/habitos/{habit}', [HabitsController::class, 'destroy'])->name('habitos.destroy');
    Route::post('/objetivos/metas', [GoalsController::class, 'store'])->name('metas.store');
    Route::put('/objetivos/metas/{goal}', [GoalsController::class, 'update'])->name('metas.update');
    Route::delete('/objetivos/metas/{goal}', [GoalsController::class, 'destroy'])->name('metas.destroy');
    Route::get('/treinos', [TreinosController::class, 'index'])->name('treinos');
    Route::get('/treinos/gerenciar', [WorkoutsController::class, 'manage'])->name('workouts.manage');
    Route::post('/treinos/workouts', [WorkoutsController::class, 'store'])->name('workouts.store');
    Route::get('/treinos/workouts/{workout}/editar', [WorkoutsController::class, 'edit'])->name('workouts.edit');
    Route::put('/treinos/workouts/{workout}', [WorkoutsController::class, 'update'])->name('workouts.update');
    Route::delete('/treinos/workouts/{workout}', [WorkoutsController::class, 'destroy'])->name('workouts.destroy');
    Route::post('/treinos/workouts/{workout}/exercicios', [ExercisesController::class, 'store'])->name('exercicios.store');
    Route::put('/treinos/exercicios/{exercise}', [ExercisesController::class, 'update'])->name('exercicios.update');
    Route::delete('/treinos/exercicios/{exercise}', [ExercisesController::class, 'destroy'])->name('exercicios.destroy');
    Route::post('/treinos/exercicios/{exercise}/series/{serie}/toggle', [ExerciseSetsController::class, 'toggle'])->name('series.toggle');
    Route::put('/treinos/exercicios/{exercise}/series/{serie}', [ExerciseSetsController::class, 'update'])->name('series.update');
    Route::post('/treinos/exercicios/{exercise}/concluir', [ExerciseSetsController::class, 'completeAll'])->name('exercicios.concluir');
    Route::get('/anotacoes', [AnotacoesController::class, 'index'])->name('anotacoes');
    Route::post('/anotacoes/books', [BooksController::class, 'store'])->name('books.store');
    Route::put('/anotacoes/books/{book}', [BooksController::class, 'update'])->name('books.update');
    Route::delete('/anotacoes/books/{book}', [BooksController::class, 'destroy'])->name('books.destroy');
    Route::get('/anotacoes/books/{book}/paginas', [BooksController::class, 'pages'])->name('books.pages');
    Route::post('/anotacoes/books/{book}/paginas', [PagesController::class, 'store'])->name('paginas.store');
    Route::get('/anotacoes/paginas/{page}', [PagesController::class, 'show'])->name('paginas.show');
    Route::put('/anotacoes/paginas/{page}', [PagesController::class, 'update'])->name('paginas.update');
    Route::delete('/anotacoes/paginas/{page}', [PagesController::class, 'destroy'])->name('paginas.destroy');
    Route::get('/alimentacao', [AlimentacaoController::class, 'index'])->name('alimentacao');
    Route::post('/alimentacao/refeicoes', [MealsController::class, 'store'])->name('refeicoes.store');
    Route::put('/alimentacao/refeicoes/{meal}', [MealsController::class, 'update'])->name('refeicoes.update');
    Route::delete('/alimentacao/refeicoes/{meal}', [MealsController::class, 'destroy'])->name('refeicoes.destroy');
    Route::post('/alimentacao/refeicoes/{meal}/toggle', [MealsController::class, 'toggle'])->name('refeicoes.toggle');
    Route::post('/alimentacao/agua', [MealsController::class, 'water'])->name('agua.store');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
