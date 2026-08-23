(function ($) {
    'use strict';

    var frame;

    function setArtwork(id, url) {
        $('[data-gmrc-canonical-image-id]').val(id || '');
        var preview = $('[data-gmrc-canonical-image-preview]');
        if (url) {
            preview.attr('src', url).prop('hidden', false);
            $('[data-gmrc-canonical-image-empty]').prop('hidden', true);
        } else {
            preview.attr('src', '').prop('hidden', true);
            $('[data-gmrc-canonical-image-empty]').prop('hidden', false);
        }
    }


    $(document).on('input', '[data-gmrc-canonical-filter]', function () {
        var query = String($(this).val() || '').toLowerCase().trim();
        $('[data-gmrc-canonical-name]').each(function () {
            var name = String($(this).data('gmrc-canonical-name') || '');
            $(this).prop('hidden', query !== '' && name.indexOf(query) === -1);
        });
    });

    $(document).on('click', '[data-gmrc-canonical-image-select]', function (event) {
        event.preventDefault();
        if (frame) {
            frame.open();
            return;
        }

        frame = wp.media({
            title: 'Choose Bestiary artwork',
            button: { text: 'Use this illustration' },
            library: { type: 'image' },
            multiple: false
        });

        frame.on('select', function () {
            var attachment = frame.state().get('selection').first().toJSON();
            var url = attachment.sizes && attachment.sizes.medium
                ? attachment.sizes.medium.url
                : attachment.url;
            setArtwork(attachment.id, url);
        });

        frame.open();
    });

    $(document).on('click', '[data-gmrc-canonical-image-remove]', function (event) {
        event.preventDefault();
        setArtwork('', '');
    });
})(jQuery);
