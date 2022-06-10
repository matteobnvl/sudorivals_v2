$(()=>{
    $('[data-form-login]').on('click',function(){
        const login = $(this).attr('data-form-login');
        const password = $(this).attr('data-form-pass');

        $('input[name="email"]').val(login);
        $('input[name="password"]').val(password);
    })
});