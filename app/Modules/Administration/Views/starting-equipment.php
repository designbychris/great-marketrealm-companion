<?php

defined('ABSPATH') || exit;

$selectedPackage = $selectedPackage ?? null;
$selectedPackageOverridden = ! empty($selectedPackageOverridden);
$startingEquipmentCoverage = is_array($startingEquipmentCoverage ?? null) ? $startingEquipmentCoverage : [];
$packageCoverage = $startingEquipmentCoverage['packages'] ?? [];
$registerUrl = add_query_arg(
    ['page' => 'gmrc-stewards-office', 'section' => 'starting-equipment'],
    admin_url('admin.php')
);
?>
<div class="wrap gmrc-admin gmrc-stewards-office gmrc-canonical-steward">
    <header class="gmrc-stewards-office__hero">
        <p class="gmrc-stewards-office__eyebrow">Canonical Records · Equipment Stewardship</p>
        <h1>Starting Equipment Packages</h1>
        <p>Maintain certified kits offered to future adventurers. Existing Character inventories remain historical snapshots.</p>
    </header>

    <?php if (isset($_GET['gmrc_equipment_saved'])) : ?><div class="notice notice-success is-dismissible"><p>The starting equipment package has been sealed.</p></div><?php endif; ?>
    <?php if (isset($_GET['gmrc_equipment_reset'])) : ?><div class="notice notice-success is-dismissible"><p>The package has been restored to its Companion baseline.</p></div><?php endif; ?>
    <?php if (isset($_GET['gmrc_equipment_error'])) : ?><div class="notice notice-error"><p><?php echo esc_html(rawurldecode((string) $_GET['gmrc_equipment_error'])); ?></p></div><?php endif; ?>

    <section class="gmrc-stewards-office__card" aria-labelledby="gmrc-equipment-coverage-title">
        <p class="gmrc-stewards-office__eyebrow">Certification &amp; Coverage</p>
        <h2 id="gmrc-equipment-coverage-title">Starter-kit audit</h2>
        <p><strong><?php echo esc_html((string) ($startingEquipmentCoverage['certified_callings'] ?? 0)); ?>/<?php echo esc_html((string) ($startingEquipmentCoverage['calling_count'] ?? 0)); ?> Callings covered</strong> · <?php echo esc_html((string) ($startingEquipmentCoverage['certified_packages'] ?? 0)); ?>/<?php echo esc_html((string) ($startingEquipmentCoverage['package_count'] ?? 0)); ?> packages certified · <?php echo esc_html((string) ($startingEquipmentCoverage['missing_armoury_links'] ?? 0)); ?> missing Armoury links.</p>
        <?php $sourceCounts = is_array($startingEquipmentCoverage['source_counts'] ?? null) ? $startingEquipmentCoverage['source_counts'] : []; ?>
        <p><strong>Provenance:</strong> <?php echo esc_html(implode(' · ', array_map(static fn ($source, $count): string => (string) $count . ' × ' . (string) $source, array_keys($sourceCounts), array_values($sourceCounts)))); ?></p>
        <p><?php echo esc_html((string) ($startingEquipmentCoverage['background_policy'] ?? '')); ?></p>
        <p><span class="gmrc-stewards-office__status"><?php echo ! empty($startingEquipmentCoverage['certified']) ? 'Coverage certified' : 'Coverage needs attention'; ?></span></p>
    </section>

    <div class="gmrc-canonical-steward__layout">
        <nav class="gmrc-canonical-steward__register" aria-label="Starting equipment package register">
            <h2>Package Register</h2>
            <ul>
                <?php foreach ($startingEquipmentPackages as $package) :
                    $url = add_query_arg(['page' => 'gmrc-stewards-office', 'section' => 'starting-equipment', 'package' => $package->id()], admin_url('admin.php'));
                    $coverage = $packageCoverage[$package->id()] ?? [];
                    ?>
                    <li><a href="<?php echo esc_url($url); ?>"<?php echo $selectedPackage && $selectedPackage->id() === $package->id() ? ' aria-current="page"' : ''; ?>><strong><?php echo esc_html($package->label()); ?></strong><small><?php echo esc_html(ucfirst($package->classKey())); ?> · <?php echo ! empty($coverage['certified']) ? 'Certified' : 'Review'; ?><?php echo ! empty($coverage['is_default']) ? ' · Default' : ''; ?></small></a></li>
                <?php endforeach; ?>
            </ul>
        </nav>

        <main class="gmrc-canonical-steward__editor">
            <?php if (! $selectedPackage) : ?>
                <section class="gmrc-stewards-office__card"><h2>Select a starting kit</h2><p>Choose a Calling package to inspect its canonical Armoury mappings.</p></section>
            <?php else :
                $selectedCoverage = $packageCoverage[$selectedPackage->id()] ?? [];
                ?>
                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                    <input type="hidden" name="action" value="gmrc_save_starting_equipment_package">
                    <input type="hidden" name="package_id" value="<?php echo esc_attr($selectedPackage->id()); ?>">
                    <?php wp_nonce_field('gmrc_save_starting_equipment_package_' . $selectedPackage->id(), 'gmrc_starting_equipment_nonce'); ?>
                    <header><div><p class="gmrc-stewards-office__eyebrow">Future <?php echo esc_html(ucfirst($selectedPackage->classKey())); ?> Characters</p><h2><?php echo esc_html($selectedPackage->label()); ?></h2></div><span class="gmrc-stewards-office__status"><?php echo esc_html($selectedPackageOverridden ? 'Steward override active' : 'Certified baseline'); ?></span></header>
                    <p><strong>Coverage:</strong> <?php echo ! empty($selectedCoverage['certified']) ? 'Certified' : 'Needs attention'; ?><?php echo ! empty($selectedCoverage['is_default']) ? ' · Default package for this Calling' : ''; ?></p>
                    <?php if (! empty($selectedCoverage['issues'])) : ?><ul><?php foreach ($selectedCoverage['issues'] as $issue) : ?><li><?php echo esc_html((string) $issue); ?></li><?php endforeach; ?></ul><?php endif; ?>
                    <label><strong>Package name</strong><input type="text" name="label" required value="<?php echo esc_attr($selectedPackage->label()); ?>"></label>
                    <label><strong>Armoury item IDs</strong><textarea name="item_ids" rows="10" required><?php $itemLines = []; foreach ($selectedPackage->items() as $itemId => $quantity) { for ($i = 0; $i < (int) $quantity; $i++) { $itemLines[] = $itemId; } } echo esc_textarea(implode("\n", $itemLines)); ?></textarea></label>
                    <p class="description">One canonical Armoury item ID per line. Repeating an ID grants multiple copies. Unknown IDs are rejected.</p>
                    <p><strong>Source:</strong> <?php echo esc_html($selectedPackage->source()); ?></p>
                    <div class="gmrc-canonical-steward__actions"><button class="button button-primary button-large" type="submit">Seal Equipment Package</button><a class="button" href="<?php echo esc_url($registerUrl); ?>">Back to register</a></div>
                </form>
                <?php if ($selectedPackageOverridden) : ?>
                    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" onsubmit="return confirm('Restore this starting kit to its certified baseline?');">
                        <input type="hidden" name="action" value="gmrc_reset_starting_equipment_package"><input type="hidden" name="package_id" value="<?php echo esc_attr($selectedPackage->id()); ?>">
                        <?php wp_nonce_field('gmrc_reset_starting_equipment_package_' . $selectedPackage->id(), 'gmrc_starting_equipment_reset_nonce'); ?>
                        <button class="button-link-delete" type="submit">Restore certified baseline</button>
                    </form>
                <?php endif; ?>
            <?php endif; ?>
        </main>
    </div>
</div>
