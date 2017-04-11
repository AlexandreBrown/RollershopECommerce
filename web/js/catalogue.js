$( document ).ready(function() {
    $(".heartQuickView").hover(
            function() {
            $(".heartQuickView i").removeClass( "fa-heart-o" );
            $(".heartQuickView i").addClass( "fa-heart" );
        }, function() {
            $(".heartQuickView i").removeClass( "fa-heart" );
            $(".heartQuickView i").addClass( "fa-heart-o" );
        }
    );
});