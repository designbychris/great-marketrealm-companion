<?php

defined('ABSPATH') || exit;

$deleteType = sanitize_key((string) ($deleteType ?? ''));
$deleteKey = sanitize_key((string) ($deleteKey ?? ''));
$deleteLabel = (string) ($deleteLabel ?? 'Steward record');
?>
<?php if (isset($_GET['gmrc_workshop_deleted'])) : ?>
<div class="notice notice-success is-dismissible"><p>The Steward record was permanently deleted.</p></div>
<?php endif; ?>
<?php if (isset($_GET['gmrc_workshop_delete_error'])) : ?>
<div class="notice notice-error"><p><?php echo esc_html(rawurldecode((string) $_GET['gmrc_workshop_delete_error'])); ?></p></div>
<?php endif; ?>
<?php if ($deleteType !== '' && $deleteKey !== '') : ?>
<section class="gmrc-steward-delete" aria-labelledby="gmrc-steward-delete-title">
    <h3 id="gmrc-steward-delete-title">Danger zone</h3>
    <p><strong>Archive for normal retirement.</strong> Permanent deletion is intended for mistakes and test records. The Companion will refuse deletion while Characters or Encounters still depend on this record.</p>
    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" onsubmit="return window.confirm('Permanently delete <?php echo esc_js($deleteLabel); ?>? This cannot be undone.');">
        <input type="hidden" name="action" value="gmrc_delete_steward_record">
        <input type="hidden" name="record_type" value="<?php echo esc_attr($deleteType); ?>">
        <input type="hidden" name="record_key" value="<?php echo esc_attr($deleteKey); ?>">
        <?php wp_nonce_field('gmrc_delete_steward_' . $deleteType . '_' . $deleteKey, 'gmrc_steward_delete_nonce'); ?>
        <button type="submit" class="button button-link-delete">Delete permanently</button>
    </form>
</section>
<?php endif; ?>
