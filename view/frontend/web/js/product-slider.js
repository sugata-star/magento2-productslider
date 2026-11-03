define(['jquery'], function ($) {
    'use strict';

    return function (config, element) {
        var $root = $(element),
            track = $root.find('.cps-track')[0],
            timer = null;

        if (!track) {
            return;
        }

        function scrollByPage(direction) {
            var maxLeft = track.scrollWidth - track.clientWidth;

            if (direction > 0 && track.scrollLeft >= maxLeft - 5) {
                track.scrollTo({left: 0, behavior: 'smooth'});
            } else if (direction < 0 && track.scrollLeft <= 5) {
                track.scrollTo({left: maxLeft, behavior: 'smooth'});
            } else {
                track.scrollBy({left: direction * track.clientWidth, behavior: 'smooth'});
            }
        }

        $root.find('.cps-prev').on('click', function () { scrollByPage(-1); });
        $root.find('.cps-next').on('click', function () { scrollByPage(1); });

        if (config.autoplay) {
            timer = setInterval(function () { scrollByPage(1); }, config.autoplay);
            $root.on('mouseenter focusin', function () { clearInterval(timer); timer = null; });
            $root.on('mouseleave focusout', function () {
                if (!timer) {
                    timer = setInterval(function () { scrollByPage(1); }, config.autoplay);
                }
            });
        }
    };
});
