<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Administration\Workshop;

defined('ABSPATH') || exit;

/**
 * Publication gate for Steward-authored Folk and Heritage mechanics.
 *
 * Drafts stay flexible; only published records must satisfy this contract.
 */
final class CustomFolkCertification
{
    /** @return array<int,string> */
    public function errors(array $folk): array
    {
        $errors = [];
        $folkKey = is_scalar($folk['key'] ?? null) ? (string) $folk['key'] : '';

        foreach ((array) ($folk['heritages'] ?? []) as $heritage) {
            if (! is_array($heritage)) {
                $errors[] = 'Every published Heritage must be a structured record.';
                continue;
            }

            $name = is_scalar($heritage['name'] ?? null)
                ? trim((string) $heritage['name'])
                : 'Heritage';
            if (($heritage['parent'] ?? '') !== $folkKey) {
                $errors[] = $name . ' must inherit from its published parent Folk.';
            }

            $mechanics = is_array($heritage['mechanics'] ?? null)
                ? $heritage['mechanics']
                : [];
            $errors = array_merge($errors, $this->mechanicErrors($name, $mechanics));
        }

        return array_values(array_unique($errors));
    }

    /** @return array<int,string> */
    private function mechanicErrors(string $name, array $mechanics): array
    {
        $errors = [];

        $size = $mechanics['size'] ?? '';
        if (is_scalar($size) && trim((string) $size) !== '' && ! in_array(
            trim((string) $size),
            ['Tiny', 'Small', 'Medium', 'Large', 'Small or Medium'],
            true
        )) {
            $errors[] = $name . ' has an unrecognised Heritage size override.';
        }

        $speed = $mechanics['speed'] ?? '';
        if ($speed !== '' && $speed !== null) {
            if (! is_numeric($speed) || (int) $speed < 0 || (int) $speed > 120 || (int) $speed % 5 !== 0) {
                $errors[] = $name . ' must use a Heritage speed from 0–120 feet in five-foot increments.';
            }
        }

        foreach ((array) ($mechanics['features'] ?? []) as $feature) {
            if (! is_array($feature)
                || trim((string) ($feature['name'] ?? '')) === ''
                || trim((string) ($feature['description'] ?? '')) === '') {
                $errors[] = $name . ' has an incomplete named Heritage trait.';
            }
        }

        foreach ((array) ($mechanics['proficiency_choices'] ?? []) as $choice) {
            if (! is_array($choice)) {
                $errors[] = $name . ' has an incomplete Heritage proficiency choice.';
                continue;
            }
            $from = is_array($choice['from'] ?? null) ? array_filter($choice['from'], 'is_scalar') : [];
            $choose = max(1, (int) ($choice['choose'] ?? 1));
            if (trim((string) ($choice['name'] ?? '')) === '' || count($from) < $choose) {
                $errors[] = $name . ' has an incomplete Heritage proficiency choice.';
            }
        }

        return $errors;
    }
}
