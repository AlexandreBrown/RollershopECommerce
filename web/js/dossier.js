$(document).ready(function() {

    $(".codePostal").mask('S0S 0S0', {placeholder: "___ ___"});
    $(".codePostal").keydown(function() {
        $(this).val($(this).val().toUpperCase());
    });

    $(".telephone").mask('000-000-0000', {placeholder: "___-___-___"});
});