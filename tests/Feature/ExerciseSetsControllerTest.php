<?php

namespace Tests\Feature;

use App\Models\{Exercise, ExerciseSet, User, Workout};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExerciseSetsControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Exercise $exercise;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $workout = Workout::create([
            'user_id'    => $this->user->id,
            'nome'       => 'Treino A',
            'dia_semana' => 1,
            'ativo'      => true,
        ]);

        $this->exercise = Exercise::create([
            'user_id'    => $this->user->id,
            'workout_id' => $workout->id,
            'nome'       => 'Supino Inclinado ( Halteres ou Máquina )',
            'tipo'       => 'forca',
            'series'     => 4,
            'rep_min'    => 8,
            'rep_max'    => 12,
            'icone'      => 'dumbbell',
            'ordem'      => 1,
        ]);
    }

    // ─── TOGGLE ───────────────────────────────────────────────────────────────

    /** Primeira vez que togela: cria ExerciseSet com feita=true */
    public function test_toggle_cria_serie_nova_como_feita(): void
    {
        $this->actingAs($this->user)
            ->postJson(route('series.toggle', [$this->exercise, 1]))
            ->assertOk()
            ->assertJson(['ok' => true, 'feita' => true]);

        $this->assertDatabaseHas('exercise_sets', [
            'exercise_id' => $this->exercise->id,
            'serie_num'   => 1,
            'feita'       => true,
        ]);
    }

    /** Segunda vez que togela: inverte para false */
    public function test_toggle_inverte_serie_existente_para_false(): void
    {
        ExerciseSet::create([
            'user_id'     => $this->user->id,
            'exercise_id' => $this->exercise->id,
            'data'        => today()->toDateString(),
            'serie_num'   => 1,
            'feita'       => true,
        ]);

        $this->actingAs($this->user)
            ->postJson(route('series.toggle', [$this->exercise, 1]))
            ->assertOk()
            ->assertJson(['ok' => true, 'feita' => false]);

        $this->assertDatabaseHas('exercise_sets', [
            'exercise_id' => $this->exercise->id,
            'serie_num'   => 1,
            'feita'       => false,
        ]);
    }

    /** Terceira vez que togela: volta para true */
    public function test_toggle_inverte_serie_false_para_true(): void
    {
        ExerciseSet::create([
            'user_id'     => $this->user->id,
            'exercise_id' => $this->exercise->id,
            'data'        => today()->toDateString(),
            'serie_num'   => 2,
            'feita'       => false,
        ]);

        $this->actingAs($this->user)
            ->postJson(route('series.toggle', [$this->exercise, 2]))
            ->assertOk()
            ->assertJson(['ok' => true, 'feita' => true]);
    }

    /** Sem Accept: application/json → redireciona (sem JSON) */
    public function test_toggle_redireciona_quando_sem_accept_json(): void
    {
        $this->actingAs($this->user)
            ->post(route('series.toggle', [$this->exercise, 1]))
            ->assertRedirect();
    }

    /** Série fora do range (> series do exercício) → 422 */
    public function test_toggle_rejeita_serie_acima_do_maximo(): void
    {
        $this->actingAs($this->user)
            ->postJson(route('series.toggle', [$this->exercise, 99]))
            ->assertStatus(422);
    }

    /** Série zero → 422 */
    public function test_toggle_rejeita_serie_zero(): void
    {
        $this->actingAs($this->user)
            ->postJson(route('series.toggle', [$this->exercise, 0]))
            ->assertStatus(422);
    }

    /** Sem autenticação → redireciona para login */
    public function test_toggle_exige_autenticacao(): void
    {
        $this->postJson(route('series.toggle', [$this->exercise, 1]))
            ->assertStatus(401);
    }

    // ─── CONCLUIR TODAS ───────────────────────────────────────────────────────

    /** Marca todas as séries do exercício como feitas */
    public function test_concluir_marca_todas_as_series(): void
    {
        $this->actingAs($this->user)
            ->postJson(route('exercicios.concluir', $this->exercise))
            ->assertOk()
            ->assertJson(['ok' => true]);

        for ($n = 1; $n <= $this->exercise->series; $n++) {
            $this->assertDatabaseHas('exercise_sets', [
                'exercise_id' => $this->exercise->id,
                'serie_num'   => $n,
                'feita'       => true,
            ]);
        }
    }

    /** Se algumas séries já existiam, apenas as false são atualizadas para true */
    public function test_concluir_atualiza_series_existentes_para_true(): void
    {
        // Série 1 já existe como feita, série 2 existe como false
        ExerciseSet::create([
            'user_id'     => $this->user->id,
            'exercise_id' => $this->exercise->id,
            'data'        => today()->toDateString(),
            'serie_num'   => 1,
            'feita'       => true,
        ]);
        ExerciseSet::create([
            'user_id'     => $this->user->id,
            'exercise_id' => $this->exercise->id,
            'data'        => today()->toDateString(),
            'serie_num'   => 2,
            'feita'       => false,
        ]);

        $this->actingAs($this->user)
            ->postJson(route('exercicios.concluir', $this->exercise))
            ->assertOk();

        // Todas as 4 séries devem estar feitas
        for ($n = 1; $n <= $this->exercise->series; $n++) {
            $this->assertDatabaseHas('exercise_sets', [
                'exercise_id' => $this->exercise->id,
                'serie_num'   => $n,
                'feita'       => true,
            ]);
        }
    }

    /** Sem autenticação → não autorizado */
    public function test_concluir_exige_autenticacao(): void
    {
        $this->postJson(route('exercicios.concluir', $this->exercise))
            ->assertStatus(401);
    }
}
