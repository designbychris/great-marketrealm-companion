<?php
/**
 * Guild Ledger component.
 *
 * Represents the outer magical book that contains one or more Guild Pages.
 *
 * Variables:
 *
 * $content string Rendered ledger page content.
 * $layout  string Optional. single, spread or stacked.
 * $class   string Optional additional classes.
 */

defined('ABSPATH') || exit;

$content = isset($content)
	? (string) $content
	: '';

$layout = isset($layout)
	? sanitize_key((string) $layout)
	: 'single';

$class = isset($class)
	? trim((string) $class)
	: '';

$allowed_layouts = array(
	'single',
	'spread',
	'stacked',
);

if (!in_array($layout, $allowed_layouts, true)) {
	$layout = 'single';
}

$classes = array(
	'guild-ledger',
	'guild-ledger--' . $layout,
);

$page_classes = array(
	'guild-ledger__pages',
	'guild-ledger__pages--' . $layout,
);

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

<div class="<?php echo esc_attr(implode(' ', array_filter($classes))); ?>">
	<div
		class="<?php echo esc_attr(
			implode(' ', array_filter($page_classes))
		); ?>"
	>
		<?php
		echo $content;
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		?>
	</div>
</div>
