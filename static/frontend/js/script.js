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

    $("#file-upload").click(function(){
	    $("#videoUpForm").toggleClass("block");
	});

	$('#publishedDate').click(function(){
	    $(".date-pic-area").toggleClass("block");
	});

	$('#videoCategory').click(function(){
	    $(".upload-catagory").toggleClass("block");
	});

	$('#main_url').click(function(){
	    $(".urlUpload").toggleClass("block");
	});
})


// function counter(div, value) {

    // Set the date we're counting down to
// var countDownDate = new Date(value).getTime();

//     // Update the count down every 1 second
//     var x = setInterval(function() {

//         // Get todays date and time
//         var now = new Date().getTime();

//         // Find the distance between now an the count down date
//         var distance = countDownDate - now;

//         //alert(distance);
//         var months = Math.floor(distance / (1000 * 60 * 60 * 24 * 30));
//         var Htmlmonth = '';
//         if(months === 1){
//             var Htmlmonth = 'Month, ';
//         }
//         else if(months > 1){
//             var Htmlmonth = 'Months, ';
//         }
//         else {
//             var months = '';
//             var Htmlmonth = '';
//         }

//         var days = Math.floor(distance / (1000 * 60 * 60 * 24) - (months *30));
//         var Htmlday = 'Day, ';
//         if(days > 1){
//             var Htmlday = 'Days, ';
//         }
//         var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
//         var htmlhour = 'Hour, ';
//         if(hours>1){
//             var htmlhour = ' Hours, ';
//         }

//         var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
//         /*var htmlminute = ' Minute ';
//         if(minutes>1){
//             var htmlminute = ' Minutes ';
//         }*/

//         var seconds = Math.floor((distance % (1000 * 60)) / 1000);
//         /*var htmlsecond = ' Second ';
//         if(hours>1){
//             var htmlsecond = ' Seconds ';
//         }*/

//         // Output the result in an element with id="demo"
//         document.getElementById(div).innerHTML = months + Htmlmonth + days + Htmlday + hours + htmlhour
//             + minutes + "MIN, " + seconds + "SEC";

//         // If the count down is over, write some text 
//         if (distance < 0) {
//             clearInterval(x);
//             document.getElementById(div).innerHTML = "Video Is Published";
//         }
//     }, 1000);
// }
function counter(div, value,defaultValue) {
    // Set the date we're counting down to
    var countDownDate = new Date(value).getTime();
    var releasetext = 'releasetext';
    // Update the count down every 1 second
    var x = setInterval(function() {

        // Get todays date and time
        var now = new Date().getTime();

        // Find the distance between now an the count down date
        var distance = countDownDate - now;

        //alert(distance);
        var months = Math.floor(distance / (1000 * 60 * 60 * 24 * 30));
        var Htmlmonth = '';
        if(months === 1){
            var Htmlmonth = 'Month, ';
        }
        else if(months > 1){
            var Htmlmonth = 'Months, ';
        }
        else {
            var months = '';
            var Htmlmonth = '';
        }

        var days = Math.floor(distance / (1000 * 60 * 60 * 24) - (months *30));
        var Htmlday = 'Day, ';
        if(days > 1){
            var Htmlday = 'Days, ';
        }
        var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        var htmlhour = 'Hour, ';
        if(hours>1){
            var htmlhour = ' Hours, ';
        }

        var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        /*var htmlminute = ' Minute ';
        if(minutes>1){
            var htmlminute = ' Minutes ';
        }*/

        var seconds = Math.floor((distance % (1000 * 60)) / 1000);
        /*var htmlsecond = ' Second ';
        if(hours>1){
            var htmlsecond = ' Seconds ';
        }*/

        // Output the result in an element with id="demo"
        document.getElementById("relesDate"+div).innerHTML = 'Time Left: '+months + Htmlmonth + days + Htmlday + hours + htmlhour
            + minutes + "MIN, " + seconds + "SEC";

        // If the count down is over, write some text 
        if (distance < 0) {
            clearInterval(x);
            document.getElementById("relesDate"+div).innerHTML = defaultValue;
            // $("#published"+div).hide();
            $("#comment"+div).hide();
            $("#description"+div).show();
            
            
        }
    }, 1000);
}