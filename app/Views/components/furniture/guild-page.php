<?php
/**
 * Guild page component.
 *
 * Variables:
 * $side    string left, right or single.
 * $content string Rendered page content.
 * $class   string Optional additional classes.
 */

defined('ABSPATH') || exit;

$side = isset($side) ? sanitize_html_class($side) : 'single';
$class = isset($class) ? $class : '';

$allowed_sides = array('left', 'right', 'single');

if (!in_array($side, $allowed_sides, true)) {
	$side = 'single';
}

$classes = array(
	'guild-page',
	'guild-page--' . $side,
);

if (!empty($class)) {
	$additional_classes = preg_split('/\s+/', $class);

	foreach ($additional_classes as $additional_class) {
		$classes[] = sanitize_html_class($additional_class);
	}
}
?>

<section class="<?php echo esc_attr(implode(' ', array_filter($classes))); ?>">

    <?php if ($spine) : ?>

        <?php
        $position = 'left';

        require GMRC_PATH
            . 'app/Views/Furniture/ledger-spine.php';
        ?>

    <?php endif; ?>

    <div class="guild-page__content">

        <?php
        echo $content;
        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        ?>

    </div>

</section>
