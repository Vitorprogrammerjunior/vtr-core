<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * Valida que a estratégia base64_encode(json_encode($ex)) usada no Blade
 * para passar dados de exercícios para Alpine.js via x-data é segura:
 *   - Não produz caracteres que quebrariam um atributo HTML (", ', <, >)
 *   - Faz round-trip correto para caracteres especiais do domínio
 */
class ExerciseXDataEncodingTest extends TestCase
{
    private function encode(array $data): string
    {
        return base64_encode(json_encode($data));
    }

    private function decode(string $encoded): array
    {
        return json_decode(base64_decode($encoded), true);
    }

    /** O output de base64_encode contém apenas chars seguros para HTML (A-Za-z0-9+/=) */
    public function test_encoding_so_contem_chars_seguros_para_html(): void
    {
        $ex = $this->exercicioBase();
        $encoded = $this->encode($ex);

        $this->assertMatchesRegularExpression('/^[A-Za-z0-9+\/=]+$/', $encoded,
            'Base64 deve conter apenas caracteres seguros para atributos HTML');
    }

    /** Nome com parênteses não quebra o encoding */
    public function test_encoding_suporta_parenteses_no_nome(): void
    {
        $ex = $this->exercicioBase(['nome' => 'Supino Inclinado (Halteres) ou Máquina']);
        $decoded = $this->decode($this->encode($ex));

        $this->assertSame($ex['nome'], $decoded['nome']);
    }

    /** Caracteres com acento (UTF-8) preservados no round-trip */
    public function test_encoding_preserva_acentos_utf8(): void
    {
        $ex = $this->exercicioBase(['nome' => 'Elevação Lateral & Frente']);
        $decoded = $this->decode($this->encode($ex));

        $this->assertSame($ex['nome'], $decoded['nome']);
    }

    /** Aspas duplas em observacao não quebram o atributo HTML */
    public function test_encoding_suporta_aspas_duplas_em_observacao(): void
    {
        $ex = $this->exercicioBase(['observacao' => 'Manter o "arco" lombar']);
        $encoded = $this->encode($ex);

        // O base64 resultante não pode conter " diretamente
        $this->assertStringNotContainsString('"', $encoded);

        $decoded = $this->decode($encoded);
        $this->assertSame($ex['observacao'], $decoded['observacao']);
    }

    /** Aspas simples em observacao são preservadas */
    public function test_encoding_suporta_aspas_simples_em_observacao(): void
    {
        $ex = $this->exercicioBase(['observacao' => "Don't lock your elbows"]);
        $decoded = $this->decode($this->encode($ex));

        $this->assertSame($ex['observacao'], $decoded['observacao']);
    }

    /** observacao nulo serializa e volta como null */
    public function test_encoding_suporta_observacao_null(): void
    {
        $ex = $this->exercicioBase(['observacao' => null]);
        $decoded = $this->decode($this->encode($ex));

        $this->assertNull($decoded['observacao']);
    }

    /** Array de séries é preservado completamente */
    public function test_encoding_preserva_array_de_series(): void
    {
        $ex = $this->exercicioBase();
        $decoded = $this->decode($this->encode($ex));

        $this->assertCount(3, $decoded['series']);
        $this->assertSame(1, $decoded['series'][0]['n']);
        $this->assertTrue($decoded['series'][0]['feita']);
        $this->assertFalse($decoded['series'][1]['feita']);
    }

    /** Round-trip completo: dado codificado e decodificado é idêntico ao original */
    public function test_round_trip_completo(): void
    {
        $ex = $this->exercicioBase([
            'nome'       => 'Agachamento (Livre) "Olímpico" com & barra',
            'observacao' => "Joelhos para fora — Don't let them cave",
        ]);

        $decoded = $this->decode($this->encode($ex));

        $this->assertSame($ex, $decoded);
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    private function exercicioBase(array $overrides = []): array
    {
        return array_merge([
            'id'         => 42,
            'nome'       => 'Supino Reto',
            'tipo'       => 'forca',
            'series'     => 3,
            'rep_min'    => 8,
            'rep_max'    => 12,
            'carga'      => 60.5,
            'observacao' => null,
            'series'     => [
                ['n' => 1, 'feita' => true],
                ['n' => 2, 'feita' => false],
                ['n' => 3, 'feita' => false],
            ],
        ], $overrides);
    }
}
