<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

trait SearchableTrait
{
    /**
     * Realiza busca completa com score em múltiplos campos.
     *
     * @param array $searchParams Array associativo com ['campo' => 'valor', 'score' => valor]
     * @return Builder
     */
    public static function multiFieldSearch(array $searchParams, int $limit = 20): Builder
    {
        $query = self::query();

        // Extrai o score mínimo, se fornecido (não será usado para filtrar, apenas para compatibilidade)
        $minScore = isset($searchParams['score']) ? (float) $searchParams['score'] : 0;
        unset($searchParams['score']); // Remove score dos parâmetros

        // Valida se há campos de busca
        if (empty($searchParams)) {
            return $query;
        }

        $conditions = [];
        $scoreExpressions = [];
        $totalTerms = 0;

        // Itera sobre os campos e valores
        foreach ($searchParams as $column => $search) {
            if (empty(trim($search))) {
                continue;
            }

            // Normaliza o termo de busca
            $searchNormalized = self::normalizeString($search);
            $terms = array_filter(explode(' ', trim($search)));
            $totalTerms += count($terms);

            // Adiciona condição WHERE para o campo
            $conditions[] = self::normalizedColumn($column) . " LIKE '%{$searchNormalized}%'";
            foreach ($terms as $index => $term) {
                $termNormalized = self::normalizeString($term);
                $conditions[] = self::normalizedColumn($column) . " LIKE '%{$termNormalized}%'";
                $scoreExpressions[] = "(CASE WHEN " . self::normalizedColumn($column) . " LIKE '%{$termNormalized}%' THEN 1 ELSE 0 END)";
            }

            // Pontuação adicional para correspondência exata da frase
            $scoreExpressions[] = "(CASE WHEN " . self::normalizedColumn($column) . " LIKE '%{$searchNormalized}%' THEN 2 ELSE 0 END)";
        }

        if (empty($conditions)) {
            return $query;
        }

        // Monta a query com score e percentual
        $query->select([
            '*',
            DB::raw("LEAST(100, GREATEST(0, (" . implode(' + ', $scoreExpressions) . ") / (" . $totalTerms . " + " . count($searchParams) . ") * 100)) AS percentual"),
            DB::raw("(" . implode(' + ', $scoreExpressions) . ") AS score")
        ]);

        // Adiciona condições WHERE
        $query->whereRaw(implode(' OR ', $conditions));
        // Adiciona condição de score mínimo
        $query->having('percentual', '>=', $minScore);


        // Ordena por score e comprimento do primeiro campo
        $firstColumn = array_key_first($searchParams);
        return $query->orderByDesc('score')
            ->orderByRaw("LENGTH({$firstColumn}) ASC")
            ->limit($limit);
    }

    /**
     * Normaliza a string removendo acentos e caracteres especiais.
     *
     * @param string $string
     * @return string
     */
    protected static function normalizeString(string $string): string
    {
        $string = mb_strtolower($string, 'UTF-8');
        $replacements = [
            '/[áàãâä]/u' => 'a',
            '/[éèêë]/u' => 'e',
            '/[íìîï]/u' => 'i',
            '/[óòõôö]/u' => 'o',
            '/[úùûü]/u' => 'u',
            '/[ç]/u' => 'c',
            '/[.,;]/u' => '',
        ];

        return preg_replace(
            array_keys($replacements),
            array_values($replacements),
            $string
        );
    }

    /**
     * Gera a expressão SQL para a coluna normalizada.
     *
     * @param string $column
     * @return string
     */
    protected static function normalizedColumn(string $column): string
    {
        return "LOWER(
            REPLACE(
                REPLACE(
                    REPLACE(
                        REPLACE(
                            REPLACE(
                                REPLACE(
                                    REPLACE(
                                        {$column},
                                        'á', 'a'
                                    ),
                                    'à', 'a'
                                ),
                                'ã', 'a'
                            ),
                            'â', 'a'
                        ),
                        'ä', 'a'
                    ),
                    'ç', 'c'
                ),
                '.', ''
            )
        )";
    }
}
