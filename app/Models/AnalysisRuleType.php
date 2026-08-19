<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AnalysisRuleType extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'description',
        'default_severity',
        'config_schema',
        'executor_class',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'config_schema' => 'array',
        ];
    }

    /**
     * @return HasMany<AnalysisRule, $this>
     */
    public function rules(): HasMany
    {
        return $this->hasMany(AnalysisRule::class, 'rule_type_id');
    }

    /**
     * @return array<string, mixed>
     */
    public function defaultConfig(): array
    {
        $config = [];

        foreach ($this->config_schema as $field) {
            $value = $field['default'] ?? null;

            if (($field['type'] ?? null) === 'keywords') {
                if (is_string($value)) {
                    $config[$field['key']] = array_values(array_filter(
                        array_map('trim', explode(',', $value)),
                        fn ($keyword) => $keyword !== '',
                    ));

                    continue;
                }

                if (is_array($value)) {
                    $config[$field['key']] = array_values(array_filter(
                        array_map('trim', array_map('strval', $value)),
                        fn ($keyword) => $keyword !== '',
                    ));

                    continue;
                }
            }

            $config[$field['key']] = $value;
        }

        return $config;
    }
}
