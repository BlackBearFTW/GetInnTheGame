$(function(){  
    $("#TitelAll").show();
    $("#TitelConnecting").hide();
    $("#TitelSharing").hide();
    $("#TitelFailfast").hide();
    $("#TitelBeYou").hide();
    $("#TitelFun").hide();
    $("#TitelBattle").hide();
    $("#TitelExploring").hide();

$("#ALL").click(function(){                 //klikken op ALL
        console.log("ALL");
        $("#TitelConnecting").hide();       //Verberg alle andere titels
        $("#TitelSharing").hide();
        $("#TitelFailfast").hide();
        $("#TitelBeYou").hide();
        $("#TitelFun").hide();
        $("#TitelBattle").hide();
        $("#TitelExploring").hide();
        $("#TitelAll").show();              //Show titel ALL
    });

$("#Connecting").click(function(){          //klikken op Connecting
        console.log("Connecting");
        $("#TitelAll").hide();              //Verberg alle andere titels
        $("#TitelSharing").hide();
        $("#TitelFailfast").hide();
        $("#TitelBeYou").hide();
        $("#TitelFun").hide();
        $("#TitelBattle").hide();
        $("#TitelExploring").hide();
        $("#TitelConnecting").show();        //Show titel Connecting
    });

$("#Sharing").click(function(){              //klikken op Sharing
        console.log("Sharing");
        $("#TitelConnecting").hide();        //Verberg alle andere titels
        $("#TitelAll").hide();
        $("#TitelFailfast").hide();
        $("#TitelBeYou").hide();
        $("#TitelFun").hide();
        $("#TitelBattle").hide();
        $("#TitelExploring").hide();
        $("#TitelSharing").show();          //Show titel Sharing
    });

$("#Failfast").click(function(){            //klikken op Failfast
        console.log("Failfast");
        $("#TitelConnecting").hide();       //Verberg alle andere titels
        $("#TitelSharing").hide();
        $("#TitelAll").hide();
        $("#TitelBeYou").hide();
        $("#TitelFun").hide();
        $("#TitelBattle").hide();
        $("#TitelExploring").hide();
        $("#TitelFailfast").show();         //Show titel FailFast
    });

$("#BeYou").click(function(){               //klikken op BeYOu
        console.log("Beyou");
        $("#TitelConnecting").hide();       //Verberg alle andere titels
        $("#TitelSharing").hide();
        $("#TitelFailfast").hide();
        $("#TitelAll").hide();
        $("#TitelFun").hide();
        $("#TitelBattle").hide();
        $("#TitelExploring").hide();
        $("#TitelBeYou").show();            //Show titel BeYou
    });

$("#Fun").click(function(){                 //klikken op Fun
        console.log("Fun");
        $("#TitelConnecting").hide();       //Verberg alle andere titels
        $("#TitelSharing").hide();
        $("#TitelFailfast").hide();
        $("#TitelBeYou").hide();
        $("#TitelAll").hide();
        $("#TitelBattle").hide();
        $("#TitelExploring").hide();
        $("#TitelFun").show();              //Show titel Fun
    });

$("#Battle").click(function(){              //klikken op Battle
        console.log("Battle");
        $("#TitelConnecting").hide();       //Verberg alle andere titels
        $("#TitelSharing").hide();
        $("#TitelFailfast").hide();
        $("#TitelBeYou").hide();
        $("#TitelFun").hide();
        $("#TitelAll").hide();
        $("#TitelExploring").hide();
        $("#TitelBattle").show();           //Show titel Battle
    });

$("#Exploring").click(function(){           //klikken op Exploring
        console.log("Exploring");
        $("#TitelConnecting").hide();       //Verberg alle andere titels
        $("#TitelSharing").hide();
        $("#TitelFailfast").hide();
        $("#TitelBeYou").hide();
        $("#TitelFun").hide();
        $("#TitelBattle").hide();
        $("#TitelAll").hide();
        $("#TitelExploring").show();        
    
    
    
    
    //Show titel Exploring
    });
    
});


