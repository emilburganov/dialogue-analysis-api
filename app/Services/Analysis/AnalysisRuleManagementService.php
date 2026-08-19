<?php

namespace App\Services\Analysis;

use App\Models\AnalysisRule;
use App\Models\AnalysisRuleType;
use App\Models\DialogueAnalysisEvent;
use App\Models\User;
use App\Services\Analysis\DTO\AnalysisRuleDTO;
use App\Services\Analysis\DTO\AnalysisRuleTypeDTO;
use App\Services\Analysis\Enums\AnalysisSeverity;
use App\Services\Analysis\Exceptions\AnalysisRuleAccessDeniedException;
use App\Services\Analysis\Exceptions\AnalysisRuleImmutableException;
use App\Services\Analysis\Exceptions\AnalysisRuleNotFoundException;
use App\Services\Analysis\Exceptions\AnalysisRuleValidationException;
use Illuminate\Support\Str;

class AnalysisRuleManagementService
{
    public function __construct(
        private readonly AnalysisRuleRegistry $registry,
    ) {}

    /**
     * @return list<AnalysisRuleTypeDTO>
     */
    public function listTypes(User $user): array
    {
        $this->ensureAdmin($user);

        return array_map(
            fn (string $type, array $meta) => new AnalysisRuleTypeDTO(
                id: $meta['id'],
                type: $type,
                name: $meta['name'],
                description: $meta['description'],
                defaultSeverity: $meta['default_severity'],
                configSchema: $meta['config_schema'],
            ),
            array_keys($this->registry->types()),
            $this->registry->types(),
        );
    }

    /**
     * @return list<AnalysisRuleDTO>
     */
    public function list(User $user): array
    {
        $this->ensureAdmin($user);

        return AnalysisRule::query()
            ->with('type')
            ->orderBy('name')
            ->get()
            ->map(fn (AnalysisRule $rule) => $this->toDto($rule))
            ->all();
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function create(User $user, array $payload): AnalysisRuleDTO
    {
        $this->ensureAdmin($user);

        $ruleTypeId = (int) ($payload['rule_type_id'] ?? 0);
        $type = $this->registry->findType($ruleTypeId);

        if ($type === null) {
            throw new AnalysisRuleValidationException('Выбран неизвестный тип правила.');
        }

        $slug = Str::slug((string) ($payload['slug'] ?? ''));

        if ($slug === '') {
            throw new AnalysisRuleValidationException('Укажите код правила (slug).');
        }

        if (AnalysisRule::query()->where('slug', $slug)->exists()) {
            throw new AnalysisRuleValidationException('Правило с таким кодом уже существует.');
        }

        $name = trim((string) ($payload['name'] ?? ''));

        if ($name === '') {
            throw new AnalysisRuleValidationException('Укажите название правила.');
        }

        $defaultSeverity = $this->normalizeSeverity(
            (string) ($payload['default_severity'] ?? $type->default_severity),
        );
        $config = $this->normalizeConfig(
            $type,
            is_array($payload['config'] ?? null) ? $payload['config'] : [],
        );

        $rule = AnalysisRule::query()->create([
            'slug' => $slug,
            'rule_type_id' => $type->id,
            'name' => $name,
            'description' => $payload['description'] ?? null,
            'default_severity' => $defaultSeverity,
            'is_enabled' => (bool) ($payload['is_enabled'] ?? true),
            'is_system' => false,
            'config' => $config,
        ]);

        return $this->toDto($rule->load('type'));
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function update(User $user, int $ruleId, array $payload): AnalysisRuleDTO
    {
        $this->ensureAdmin($user);

        $rule = $this->findRule($ruleId);
        $this->ensureMutable($rule, 'изменять');

        $name = trim((string) ($payload['name'] ?? $rule->name));

        if ($name === '') {
            throw new AnalysisRuleValidationException('Укажите название правила.');
        }

        $rule->name = $name;
        $rule->description = $payload['description'] ?? $rule->description;

        if (array_key_exists('default_severity', $payload)) {
            $rule->default_severity = $this->normalizeSeverity((string) $payload['default_severity']);
        }

        if (array_key_exists('is_enabled', $payload)) {
            $rule->is_enabled = (bool) $payload['is_enabled'];
        }

        if (array_key_exists('config', $payload) && is_array($payload['config'])) {
            $rule->loadMissing('type');

            if ($rule->type === null) {
                throw new AnalysisRuleValidationException('У правила не найден тип.');
            }

            $rule->config = $this->normalizeConfig($rule->type, $payload['config']);
        }

        $rule->save();

        return $this->toDto($rule->fresh(['type']));
    }

    public function toggle(User $user, int $ruleId): AnalysisRuleDTO
    {
        $this->ensureAdmin($user);

        $rule = $this->findRule($ruleId);
        $this->ensureMutable($rule, 'изменять');
        $rule->is_enabled = ! $rule->is_enabled;
        $rule->save();

        return $this->toDto($rule->fresh(['type']));
    }

    public function delete(User $user, int $ruleId): void
    {
        $this->ensureAdmin($user);

        $rule = $this->findRule($ruleId);
        $this->ensureMutable($rule, 'удалять');

        DialogueAnalysisEvent::query()
            ->where('analysis_rule_id', $rule->id)
            ->delete();

        $rule->delete();
    }

    private function ensureMutable(AnalysisRule $rule, string $action): void
    {
        if ($rule->is_system) {
            throw new AnalysisRuleImmutableException("Системное правило нельзя {$action}.");
        }
    }

    private function ensureAdmin(User $user): void
    {
        if (! $user->isAdmin()) {
            throw new AnalysisRuleAccessDeniedException('Управление правилами доступно только администраторам.');
        }
    }

    private function findRule(int $ruleId): AnalysisRule
    {
        $rule = AnalysisRule::query()->with('type')->find($ruleId);

        if ($rule === null) {
            throw new AnalysisRuleNotFoundException;
        }

        return $rule;
    }

    private function toDto(AnalysisRule $rule): AnalysisRuleDTO
    {
        $rule->loadMissing('type');

        return new AnalysisRuleDTO(
            id: $rule->id,
            slug: $rule->slug,
            ruleTypeId: $rule->rule_type_id,
            ruleType: $rule->type?->slug ?? '',
            name: $rule->name,
            description: $rule->description,
            defaultSeverity: $rule->default_severity,
            defaultSeverityLabel: AnalysisSeverity::from($rule->default_severity)->label(),
            isEnabled: $rule->is_enabled,
            isSystem: $rule->is_system,
            config: $rule->config,
            typeName: $rule->type?->name ?? '',
            typeDescription: $rule->type?->description ?? '',
        );
    }

    private function normalizeSeverity(string $severity): string
    {
        $allowed = array_map(fn (AnalysisSeverity $item) => $item->value, AnalysisSeverity::cases());

        if (! in_array($severity, $allowed, true)) {
            throw new AnalysisRuleValidationException('Недопустимый уровень критичности.');
        }

        return $severity;
    }

    /**
     * @param  array<string, mixed>  $config
     * @return array<string, mixed>
     */
    private function normalizeConfig(AnalysisRuleType $type, array $config): array
    {
        $schema = $type->config_schema;
        $normalized = $type->defaultConfig();

        foreach ($schema as $field) {
            $key = $field['key'];

            if (! array_key_exists($key, $config)) {
                continue;
            }

            $value = $config[$key];

            if ($field['type'] === 'integer') {
                $min = (int) ($field['min'] ?? 1);
                $normalized[$key] = max($min, (int) $value);

                continue;
            }

            if ($field['type'] === 'keywords') {
                if (is_string($value)) {
                    $normalized[$key] = array_values(array_filter(
                        array_map('trim', explode(',', $value)),
                        fn ($keyword) => $keyword !== '',
                    ));

                    continue;
                }

                if (is_array($value)) {
                    $normalized[$key] = array_values(array_filter(
                        array_map('trim', array_map('strval', $value)),
                        fn ($keyword) => $keyword !== '',
                    ));
                }
            }
        }

        return $normalized;
    }
}
