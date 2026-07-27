<?php
/**
 * Decorative stitched leather spine.
 *
 * Variables:
 *
 * @var string $position Optional. left|right. Default left.
 * @var string $class    Optional additional CSS classes.
 */

defined('ABSPATH') || exit;

$position = isset($position)
    ? sanitize_key((string) $position)
    : 'left';

$class = isset($class)
    ? trim((string) $class)
    : '';

if (!in_array($position, array('left', 'right'), true)) {
    $position = 'left';
}

$classes = array(
    'ledger-spine',
    'ledger-spine--' . $position,
);

if ($class !== '') {
    $additionalClasses = preg_split('/\s+/', $class);

    foreach ($additionalClasses as $additionalClass) {
        $additionalClass = sanitize_html_class($additionalClass);

        if ($additionalClass !== '') {
            $classes[] = $additionalClass;
        }
    }
}
?>

<div
    class="<?php echo esc_attr(implode(' ', $classes)); ?>"
    aria-hidden="true"
>
    <span class="ledger-spine__leather"></span>

    <span class="ledger-spine__stitching">
        <span class="ledger-spine__stitch"></span>
        <span class="ledger-spine__stitch"></span>
        <span class="ledger-spine__stitch"></span>
        <span class="ledger-spine__stitch"></span>
        <span class="ledger-spine__stitch"></span>
        <span class="ledger-spine__stitch"></span>
        <span class="ledger-spine__stitch"></span>
        <span class="ledger-spine__stitch"></span>
        <span class="ledger-spine__stitch"></span>
        <span class="ledger-spine__stitch"></span>
        <span class="ledger-spine__stitch"></span>
        <span class="ledger-spine__stitch"></span>
    </span>

    <span class="ledger-spine__page-shadow"></span>
</div>
