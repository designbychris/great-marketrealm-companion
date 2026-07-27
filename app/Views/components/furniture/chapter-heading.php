<?php
/**
 * Guild Ledger chapter heading.
 *
 * Variables:
 *
 * @var string $ledger      Optional ledger label.
 * @var string $volume      Optional volume label.
 * @var string $title       Chapter title.
 * @var string $description Optional introductory text.
 * @var int    $level       Heading level from 1 to 6.
 * @var string $ornament    Optional decorative character.
 * @var string $class       Optional additional CSS classes.
 */

defined('ABSPATH') || exit;

$ledger = isset($ledger)
    ? trim((string) $ledger)
    : 'The Guild Ledger';

$volume = isset($volume)
    ? trim((string) $volume)
    : '';

$title = isset($title)
    ? trim((string) $title)
    : '';

$description = isset($description)
    ? trim((string) $description)
    : '';

$level = isset($level)
    ? absint($level)
    : 1;

$ornament = isset($ornament)
    ? trim((string) $ornament)
    : '✦';

$class = isset($class)
    ? trim((string) $class)
    : '';

if ($level < 1 || $level > 6) {
    $level = 1;
}

$headingTag = 'h' . $level;

$classes = array(
    'chapter-heading',
);

if ($class !== '') {
    $additionalClasses = preg_split(
        '/\s+/',
        $class
    );

    foreach ($additionalClasses as $additionalClass) {
        $additionalClass = sanitize_html_class(
            $additionalClass
        );

        if ($additionalClass !== '') {
            $classes[] = $additionalClass;
        }
    }
}
?>

<header class="<?php echo esc_attr(
    implode(' ', $classes)
); ?>">
    <div
        class="chapter-heading__crest"
        aria-hidden="true"
    >
        <span class="chapter-heading__crest-ring">
            <?php echo esc_html($ornament); ?>
        </span>
    </div>

    <?php if ($ledger !== '') : ?>
        <p class="chapter-heading__ledger">
            <?php echo esc_html($ledger); ?>
        </p>
    <?php endif; ?>

    <div
        class="chapter-heading__divider"
        aria-hidden="true"
    >
        <span></span>

        <span class="chapter-heading__divider-ornament">
            <?php echo esc_html($ornament); ?>
        </span>

        <span></span>
    </div>

    <?php if ($volume !== '') : ?>
        <p class="chapter-heading__volume">
            <?php echo esc_html($volume); ?>
        </p>
    <?php endif; ?>

    <<?php echo esc_attr($headingTag); ?>
        class="chapter-heading__title"
    >
        <?php echo esc_html($title); ?>
    </<?php echo esc_attr($headingTag); ?>>

    <?php if ($description !== '') : ?>
        <p class="chapter-heading__description">
            <?php echo esc_html($description); ?>
        </p>
    <?php endif; ?>
</header>
