var selected = "";
var getUrl = window.location;
var baseUrl = getUrl.protocol + "//" + getUrl.host;

//NAVAGATION
$('nav a').click(function(e) {
    e.preventDefault();
	$('a').removeClass("active");
	$(this).addClass("active");

    $(".main").hide();
    var page = $(this).attr('href');
    $("#"+page).show();
    if (page == "stats"){
        sendAJAX({"action":"stats"}); // GET GAME STATS
    }
});

//START OR GUESS
$('#play_button').click(function(e) {
    selected = $('input').val();
    var action = "";

    if (!$.isNumeric(selected)){
        $("#game p").html("Please enter a number");
        return;
    }

    if (window.gameMode == 0 && selected > 1){ // STARTING NEW
        if (selected > 1 ){
            action = "start";
        } else {
            $("#game p").html("Please enter a number greater then 1");
            return;
        }
    } else if (window.gameMode == 1 && selected > 0){ // GUESSING
        if (selected > 0 ){
            action = "guess";
        } else {
            $("#game p").html("Please enter a number greater then 0");
            return;
        }
    }

    //AJAX
    sendAJAX({"action":action, "data":selected});
});

//ABANDON THE GAME
$('#stop_button').click(function(e) {
    //AJAX
    sendAJAX({"action":"end"});
});

//UPDATE GAME UI
function updateGameMode($mode){
    window.gameMode = $mode;
    $('input').val("");

    if (window.gameMode === 0 ){
        $("#stop_button").hide();
        $("#game h3").html("Select a number greater then 1");
        $("#game p").html("");
        $('#play_button').html("Play");
        $("#stop_button").hide();
    } else {
        $("#game h3").html("Guess a number between 1 and " + selected);
        $("#game p").html("");
        $('#play_button').html("Guess");
        $("#stop_button").show();
    }
}

//AJAX
function sendAJAX(post_data){
    $.ajax({
        type: "POST",
        url: baseUrl + "/php/endpoint.php",
        data: JSON.stringify(post_data),
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        success: function(data){
            if (data.code === "200"){
                if (data.status === "stats"){
                    printStats(data.message);
                } else if (data.status === "checked"){
                    if(data.message > 1){
                        selected = data.message;
                        updateGameMode(1); // STARTED
                    } else {
                        updateGameMode(0); // NOT STARTED
                    }
                } else if (data.status === "started"){
                    updateGameMode(1);
                } else if (data.status === "ended"){
                    updateGameMode(0);
                } else if (data.status === "over"){
                    $("#game p").html("The secret number is less than " + selected);
                } else if (data.status === "under"){
                    $("#game p").html("The secret number is greater than " + selected);
                } else if (data.status === "correct"){
                    //alert("YOU GUESSED CORRECTLY!");
                    $('#winModal').modal('toggle');
                    updateGameMode(0);
                }else {
                    alert("Sorry Something Went Wrong");
                }
            } else {
                alert(data.message);
            }

        },
        failure: function(errMsg) {
            alert(errMsg);
        }
    });
}

function printStats(data){
    var html = "";
    var gameid = "";

    data.forEach(game => {
        if (gameid != game[0] ){
            gameid = game[0];
            html +="<div class='list_game row'>";
            html += "<div class='col list_game_column'>Game ID <br>"+game[0]+"</div>";
            html += "<div class='col list_game_column'>Status <br>"+game[1]+"</div>";
            html += "<div class='col list_game_column'>Selected Max <br>"+game[2]+"</div>";
            html += "<div class='col list_game_column'>Secret Number <br>"+game[3]+"</div>";
            html += "<div class='col list_game_column'>Completion Time <br>"+game[7]+"</div>";
            html +="</div>";
        } else {
            html +="<div class='list_play row'>";
            html +="<div class='col list_play_column'>Guess: "+game[4]+"</div>";
            html +="<div class='col list_play_column'>Result: "+game[5]+"</div>";
            html +="<div class='col list_play_column'>Time: "+game[6]+"</div>";
            html +="</div>";
        }
    });

    $("#stats h3").html("Here are your statistics");
    $("#data").html(html);
}

//AJAX
sendAJAX({"action":"check"}); // CHECK IF STARTED

$("#stats").hide(); 
