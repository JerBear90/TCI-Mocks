const initRadios = () => {
    $(document).on( 'change', 'input[type=radio],input[type=checkbox]', function() {
        var name = $(this).attr('name');
        $inputs = $(`input[name='name']`);
        $inputs.closest('.form-group').removeClass('active');
        $(`input[name='${name}']:checked`).closest('.form-group').addClass('active');
    });
};
module.exports = initRadios;