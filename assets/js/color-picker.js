window.addEventListener('DOMContentLoaded', (event) => {
    // Color selector code.
    var h_slider = document.querySelector('#h-slider'),
        s_slider = document.querySelector('#s-slider'),
        l_slider = document.querySelector('#l-slider'),
        slider_box = document.querySelector('#color-sliders'),
        color_field = document.querySelector('#show_theme_color');
    if (!(h_slider && s_slider && l_slider && slider_box && color_field)) return;

    // Formulae from https://en.wikipedia.org/wiki/HSL_and_HSV.
    var hsl_to_rgb = function(H, S, L) {
        var a = S * Math.min(L, 1-L);
        var rgb = 1;
        for (var k = H/30; rgb < 0x1000000; k = (k + 8) % 12) {
            var x = Math.round(255 * (L - a * Math.max(Math.min(k-3, 9-k, 1), -1)));
            rgb = (rgb << 8) | x;
        }
        return '#' + rgb.toString(16).slice(-6);
    }
    var rgb_to_hsl = function(rgb) {
        var r = parseInt(rgb.substring(1, 3), 16) / 255,
            g = parseInt(rgb.substring(3, 5), 16) / 255,
            b = parseInt(rgb.substring(5, 7), 16) / 255;
        var max = Math.max(r, g, b),
            min = Math.min(r, g, b);
        switch (max) {
            case min:
                return [0, 0, max];
            case r:
                h = 360 + (60 * (g - b) / (max - min));
                break;
            case g:
                h = 120 + (60 * (b - r) / (max - min));
                break;
            case b:
                h = 240 + (60 * (r - g) / (max - min));
                break;
        }
        var l = (min + max) / 2;
        return [h % 360, (max - l)/Math.min(l, 1-l), l];
    }
    var update_from_sliders = function() {
        var rgb = hsl_to_rgb(h_slider.valueAsNumber,
            s_slider.valueAsNumber/100, l_slider.valueAsNumber/100);
        color_field.value = rgb;
        display_color(rgb, [h_slider.valueAsNumber,
            s_slider.valueAsNumber/100, l_slider.valueAsNumber/100]);
    }
    var update_from_field = function() {
        if (color_field.value && !(/^#[0-9A-Fa-f]{6}$/.test(color_field.value))) {
            // Convert invalid user input into a hex code, or blank if there are no hex digits at all.
            var temp = color_field.value.replace(/[^0-9A-Fa-f]/g, '');
            if (temp.length === 0) {
                color_field.value = '';
            } else if (temp.length === 3) {
                color_field.value = '#' + temp[0]+temp[0] + temp[1]+temp[1] + temp[2]+temp[2];
            } else {
                color_field.value = '#' + (temp + '000000').substring(0, 6);
            }
        }
        if (color_field.value) {
            var hsl = rgb_to_hsl(color_field.value);
            h_slider.value = hsl[0];
            s_slider.value = hsl[1] * 100;
            l_slider.value = hsl[2] * 100;
        }
        display_color(color_field.value, hsl);
    }
    var display_color = function(rgb, hsl) {
        if (rgb) {
            slider_box.parentNode.style.setProperty("--custom-color", rgb);
        } else {
            slider_box.parentNode.style.removeProperty("--custom-color");
        }
        slider_box.style.setProperty("--hue", hsl[0]);
        slider_box.style.setProperty("--sat", hsl[1]*100 + '%');
        slider_box.style.setProperty("--light", hsl[2]*100 + '%');
    }

    // EventListeners. 'input' gives live feedback from the sliders.
    slider_box.addEventListener('change', update_from_sliders);
    slider_box.addEventListener('input', update_from_sliders);
    color_field.addEventListener('change', update_from_field);

    update_from_field();
});
