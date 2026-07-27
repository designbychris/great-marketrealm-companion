<?php
/**
 * Decorative recipe divider.
 *
 * Variables:
 * $ornament string Optional decorative character.
 */

defined('ABSPATH') || exit;

$ornament = isset($ornament) ? $ornament : '✦';
?>

<div class="recipe-divider" aria-hidden="true">
	<span class="recipe-divider__ornament">
		<?php echo esc_html($ornament); ?>
	</span>
</div>
