<?php

namespace App\Services;

class CplAssessmentCalculator
{
    /**
     * Calculate an equally weighted CPL average from fully scored assessment courses.
     * Each course contains its positive-weight components as [weight, score].
     */
    public function calculate(array $courses, float $minimum): array
    {
        $courseScores = [];
        $missingCourseCount = 0;

        foreach ($courses as $components) {
            $components = array_values(array_filter(
                $components,
                fn (array $component) => (float) ($component['weight'] ?? 0) > 0
            ));

            $complete = $components !== [] && collect($components)->every(
                fn (array $component) => array_key_exists('score', $component)
                    && $component['score'] !== null
                    && is_numeric($component['score'])
            );

            if (!$complete) {
                $missingCourseCount++;
                continue;
            }

            $totalWeight = array_sum(array_column($components, 'weight'));
            if ($totalWeight <= 0) {
                $missingCourseCount++;
                continue;
            }

            $weightedTotal = array_sum(array_map(
                fn (array $component) => (float) $component['score'] * (float) $component['weight'],
                $components
            ));
            $courseScores[] = round($weightedTotal / $totalWeight, 2);
        }

        $incomplete = $courses === []
            || $missingCourseCount > 0
            || count($courseScores) !== count($courses);
        $score = !$incomplete
            ? round(array_sum($courseScores) / count($courseScores), 2)
            : '-';

        return [
            'score' => $score,
            'status' => $incomplete
                ? 'Belum lengkap'
                : ($score >= $minimum ? 'Tercapai' : 'Belum tercapai'),
            'missing_course_count' => $missingCourseCount,
            'complete' => !$incomplete,
        ];
    }
}
