<?php
use GreatMarketrealmCompanion\Core\Support\MarketRealmDate;
defined('ABSPATH') || exit;
$sessionId = $record->sourceValue('tabletop_session_id');
$recap = trim((string) $record->sourceData('recap')) ?: $record->content();
$contributions = $record->sourceData('contributions');
$contributions = is_array($contributions) ? $contributions : [];
$date = MarketRealmDate::date($record->sourceValue('played_date'));
$duration = max(0, (int) $record->sourceData('duration_seconds'));
$durationLabel = $duration >= 3600 ? sprintf('%dh %02dm', intdiv($duration,3600), intdiv($duration%3600,60)) : sprintf('%dm', intdiv($duration,60));
$base = home_url('/companion/');
?>
<section class="gmrc-session-ledger gmrc-fellowship-session">
<header class="gmrc-session-ledger__hero"><div><p class="gmrc-dm-desk__eyebrow">Company Chronicle · Tabletop Certified</p><h1><?php echo esc_html($record->title()); ?></h1><p><?php echo esc_html(trim($date . ($durationLabel !== '' ? ' · ' . $durationLabel : ''))); ?></p></div></header>
<section class="gmrc-session-panel"><h2>Previously, in the MarketRealm…</h2><div><?php echo nl2br(esc_html($recap)); ?></div></section>
<section class="gmrc-session-panel"><h2>Adventurers at the Table</h2><?php if ($contributions === []) : ?><p>No character-specific deeds were recorded for this Session.</p><?php else : ?><div class="gmrc-session-contributions"><?php foreach ($contributions as $row) : ?><article><h3><?php echo esc_html((string)($row['character_name'] ?? 'Adventurer')); ?></h3><?php $deeds=is_array($row['deeds']??null)?$row['deeds']:[]; ?><p><?php echo esc_html($deeds !== [] ? implode(' ', array_map('strval',$deeds)) : 'No recorded deeds during this Session.'); ?></p></article><?php endforeach; ?></div><?php endif; ?></section>
<section class="gmrc-session-panel"><h2>Player notes</h2><?php if ($notes === []) : ?><p>No additional memories have been added yet.</p><?php else : ?><?php foreach ($notes as $note) : ?><article class="gmrc-session-player-note"><p><?php echo nl2br(esc_html($note->content())); ?></p><small>Added <?php echo esc_html(MarketRealmDate::dateTime($note->recordedAt()->format(DATE_ATOM))); ?></small></article><?php endforeach; ?><?php endif; ?>
<form class="gmrc-session-note-form" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
<?php wp_nonce_field('gmrc_party_session_note_'.$party->id()->value().'_'.$sessionId,'gmrc_nonce'); ?>
<input type="hidden" name="action" value="gmrc_app_request">
<input type="hidden" name="gmrc_route" value="parties/<?php echo esc_attr($party->id()->value()); ?>/sessions/<?php echo esc_attr($sessionId); ?>/notes">
<div class="gmrc-session-note-form__intro">
    <p class="gmrc-dm-desk__eyebrow">Player memory</p>
    <h3>Add a memory</h3>
    <p>Record a detail, discovery, clue, joke or moment the Fellowship should remember from this Session.</p>
</div>
<label class="gmrc-fellowship-field gmrc-session-note-form__field">
    <span>Session note</span>
    <textarea name="content" rows="5" maxlength="3000" placeholder="What should the Fellowship remember?" required></textarea>
    <small>Player-written notes are Fellowship memories, not Tabletop-certified deeds.</small>
</label>
<div class="gmrc-session-note-form__actions">
    <button class="gmrc-fellowship-button gmrc-fellowship-button--primary" type="submit">Add to this Session</button>
</div>
</form></section>
<p class="gmrc-session-back"><a href="<?php echo esc_url(add_query_arg(['gmrc_route'=>'parties/'.$party->id()->value(),'gmrc_party_tab'=>'chronicle'],$base)); ?>">← Back to Company Chronicle</a></p>
</section>
