jQuery(document).ready(function($) {
    $('#option_selector').on('change', function() {
        var selectedValue = $(this).val();
        if (selectedValue === 'interes') {
            $('#interes_field').show();
            $('#descuento_field').hide();
        } else if (selectedValue === 'descuento') {
            $('#interes_field').hide();
            $('#descuento_field').show();
        } else {
            $('#interes_field, #descuento_field').hide();
        }
    });
});