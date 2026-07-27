<?php
/**
 * Guild page component.
 *
 * Variables:
 *
 * $side    string Left, right or single.
 * $content string Rendered page content.
 * $class   string Optional additional classes.
 * $spine   bool   Whether the leather spine should be displayed.
 */

defined('ABSPATH') || exit;

$content = isset($content)
	? (string) $content
	: '';

$side = isset($side)
	? sanitize_key((string) $side)
	: 'single';

$class = isset($class)
	? trim((string) $class)
	: '';

$spine = isset($spine)
	? (bool) $spine
	: true;

$allowed_sides = array(
	'left',
	'right',
	'single',
);

if (!in_array($side, $allowed_sides, true)) {
	$side = 'single';
}

$classes = array(
	'guild-page',
	'guild-page--' . $side,
);

if ($spine) {
	$classes[] = 'guild-page--has-spine';

	$classes[] = $side === 'right'
		? 'guild-page--spine-right'
		: 'guild-page--spine-left';
}

if ($class !== '') {
	$additional_classes = preg_split('/\s+/', $class);

	foreach ($additional_classes as $additional_class) {
		$additional_class = sanitize_html_class($additional_class);

		if ($additional_class !== '') {
			$classes[] = $additional_class;
		}
	}
}
?>

<section class="<?php echo esc_attr(implode(' ', $classes)); ?>">

	<?php if ($spine) : ?>

		<?php
		(function () use ($side): void {
			$position = $side === 'right'
				? 'right'
				: 'left';

			require GMRC_PATH
				. 'app/Views/components/furniture/ledger-spine.php';
		})();
		?>

	<?php endif; ?>

	<div class="guild-page__content">
		<?php
		// The content has already been rendered by a trusted view.
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $content;
		?>
	</div>

</section>
