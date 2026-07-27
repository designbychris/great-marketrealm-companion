<?php
/**
 * Guild page heading component.
 *
 * Variables:
 * $title       string Heading text.
 * $eyebrow     string Optional eyebrow text.
 * $description string Optional description.
 * $level       int Heading level between 1 and 6.
 */

defined('ABSPATH') || exit;

$title = isset($title) ? $title : '';
$eyebrow = isset($eyebrow) ? $eyebrow : '';
$description = isset($description) ? $description : '';
$level = isset($level) ? absint($level) : 1;

if ($level < 1 || $level > 6) {
	$level = 1;
}

$heading_tag = 'h' . $level;
?>

<header class="guild-page-heading">
	<?php if ($eyebrow !== '') : ?>
		<span class="guild-page-heading__eyebrow">
			<?php echo esc_html($eyebrow); ?>
		</span>
	<?php endif; ?>

	<<?php echo esc_attr($heading_tag); ?> class="guild-page-heading__title">
		<?php echo esc_html($title); ?>
	</<?php echo esc_attr($heading_tag); ?>>

	<?php if ($description !== '') : ?>
		<p class="guild-page-heading__description">
			<?php echo esc_html($description); ?>
		</p>
	<?php endif; ?>
</header>
