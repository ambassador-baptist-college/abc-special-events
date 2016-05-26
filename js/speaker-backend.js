jQuery(document).ready(function($){
    $('[name="post_title"]').on('blur', function(){
        var speakerName = $(this).val(),
            nameReversed = speakerName.split(' ').reverse().join('-').toLowerCase().replace('.','');

        $('#acf-field-sort_order').val(nameReversed);
    });
});
