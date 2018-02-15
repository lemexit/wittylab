/**
 * Created by rony on 12/3/2017.
 */

$(document).ready(function()
{
    $("html").removeClass("gr__localhost");
    $("html span.gr__tooltip").remove();

    $("#VideoCarousel1").carousel({interval: 3500});
    $("#VideoCarousel2").carousel({interval: 2000});
    $("#VideoCarousel3").carousel({interval: 2500});
    $("#VideoCarousel4").carousel({interval: 3000});



})