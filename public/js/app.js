$("#menuBT").click(function () {
    $("#pages").animate({
        height: 'toggle'
    });
    $("#menuBT").toggleClass("change");
});
if (window.location.pathname === "/mySudoku/login" && window.innerWidth > "768") {
    $("#Login").css("background-color","#0271C0").css("color","#FFFFFF"); 
} else if(window.location.pathname === "/mySudoku/login" && window.innerWidth < "768") {
    $("#Login").css("box-shadow","0 2px 0 #0271C0").css("color","#0271C0");
};
if (window.location.pathname === "/mySudoku/register" && window.innerWidth > "768") {
    $("#Register").css("background-color","#0271C0").css("color","#FFFFFF");   
} else if(window.location.pathname === "/mySudoku/register" && window.innerWidth < "768") {
    $("#Register").css("box-shadow","0 2px 0 #0271C0").css("color","#0271C0");
};
if (window.location.pathname === "/mySudoku/game" && window.innerWidth > "768") {
    $("#Game").css("background-color","#0271C0").css("color","#FFFFFF");
} else if(window.location.pathname === "/mySudoku/game" && window.innerWidth < "768") {
    $("#Game").css("box-shadow","0 2px 0 #0271C0").css("color","#0271C0");
};

$('.profil').click(function () {
    $('.toggle-profil').toggleClass('active')
})
$('main').click(function () {
    $('.toggle-profil').removeClass('active')
})

$('#Game').click(function () {
    $('#loader').addClass('active')
})

$('.btn-play').click(function () {
    $('#loader').addClass('active')
})

const loader = document.getElementById("loader");

document.getElementById('play').addEventListener('click', function() {
    loader.classList.add('active')
    document.querySelector('body').classList.add('active')
})
