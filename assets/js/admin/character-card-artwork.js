(function ($) {
    'use strict';

    var frame;

    function setArtwork(id, url) {
        $('[data-gmrc-card-artwork-id]').val(id || '');
        $('[data-gmrc-card-artwork-preview]')
            .attr('src', url || '')
            .prop('hidden', !url);
        $('[data-gmrc-card-artwork-empty]').prop('hidden', !!url);
        $('[data-gmrc-card-artwork-remove]').prop('hidden', !url);
    }

    $(document).on('click', '[data-gmrc-card-artwork-select]', function (event) {
        event.preventDefault();

        if (!frame) {
            frame = wp.media({
                title: 'Choose Character Creation artwork',
                button: { text: 'Use this illustration' },
                library: { type: 'image' },
                multiple: false
            });

            frame.on('select', function () {
                var attachment = frame.state().get('selection').first().toJSON();
                var url = attachment.sizes && attachment.sizes.large
                    ? attachment.sizes.large.url
                    : attachment.url;
                setArtwork(attachment.id, url);
            });
        }

        frame.open();
    });

    $(document).on('click', '[data-gmrc-card-artwork-remove]', function (event) {
        event.preventDefault();
        setArtwork('', '');
    });
})(jQuery);
