<?php
    session_start();
    $data = json_decode(file_get_contents('gamestats.json'), true);
    $id = $_SESSION["id"];
    $nid = $data["ids"][$id];
    $player = $data["players"][$nid];
    $idx = $_POST["valasztas"] ?? "";
    $lastcard = $player["hidden"][$idx] ?? "";
    $lerakott = false;
    $temeto = false;
    $cnt = $_POST["count"] ?? 1;
    function lerak(){
        global $data;
        global $player;
        global $nid;
        global $idx;
        global $cnt;
        $ertek = 0;
        if(count($player["hand"])>0){
            $data["table"][] = $player["hand"][$idx];
            $ertek = $player["hand"][$idx];
            unset($data["players"][$nid]["hand"][$idx]);
            $data["players"][$nid]["hand"]=array_values($data["players"][$nid]["hand"]);
            if(count($data["cards"])>0 && count($data["players"][$nid]["hand"])<5){
                $data["players"][$nid]["hand"][] = array_pop($data["cards"]);
            }
            for($i = 1; $i<$cnt; $i++){
                $key = array_search($ertek,$data["players"][$nid]["hand"]);
                $data["table"][] = $data["players"][$nid]["hand"][$key];
                unset($data["players"][$nid]["hand"][$key]);
                $data["players"][$nid]["hand"]=array_values($data["players"][$nid]["hand"]);
                if(count($data["cards"])>0 && count($data["players"][$nid]["hand"])<5){
                    $data["players"][$nid]["hand"][] = array_pop($data["cards"]);
                }
            }
            
        }else if(count($player["top"])>0){
            if(isset($_POST["leraktop"])){
                $data["table"][] = $player["top"][$idx];
                $ertek = $player["top"][$idx];
                unset($data["players"][$nid]["top"][$idx]);
                $data["players"][$nid]["top"]=array_values($data["players"][$nid]["top"]);

                for($i = 1; $i<$cnt; $i++){
                    $key = array_search($ertek,$data["players"][$nid]["top"]);
                    $data["table"][] = $data["players"][$nid]["top"][$key];
                    unset($data["players"][$nid]["top"][$key]);
                    $data["players"][$nid]["top"]=array_values($data["players"][$nid]["top"]);
                }

            }
            // HA KÉZBEN NINCS, TOP-RÓL!!!!

        }else if(count($player["hidden"])>0){
            if(isset($_POST["lerakhidden"])){
                $data["table"][] = $player["hidden"][$idx];
                
                unset($data["players"][$nid]["hidden"][$idx]);
                $data["players"][$nid]["hidden"]=array_values($data["players"][$nid]["hidden"]);

                
            }

        }
    }

    function felvesz(){
        global $data;
        global $player;
        global $nid;
        global $idx;
        if(isset($_POST["lerakhidden"])){
            $data["players"][$nid]["hand"][] = $player["hidden"][$idx];
            unset($data["players"][$nid]["hidden"][$idx]);
        }
        $data["players"][$nid]["hand"] = array_merge($data["players"][$nid]["hand"],$data["table"]);
        $data["table"] = [];
    }

    function nextturn(){
        global $data;
        $data["turn"]=$data["turn"]+1;
        if($data["turn"]>count($data["ids"])-1){
            $data["turn"]=0;
        }
    }

    
    function canLerak($ertek){
        global $temeto;
        global $data;
        global $cnt;
        $felso = end($data["table"]);
        $felso3 = array_slice($data["table"], -3, 3);
        if($ertek>=$felso || $ertek==2 || $ertek==10){
            if($ertek==10 || count(array_filter($felso3, fn($x) => $x == $ertek))==3 ||  count(array_filter($felso3, fn($x) => $x == $ertek))+$cnt>=4){
                $temeto=true;
            }
            return true;
        }
        else{
            return false;
        }
    }

    function canFelvesz(){
        global $data;
        return count($data["table"])>0;
    }

    if(isset($_SESSION["id"])){
        $lapertek = $player["hand"][$idx] ?? -1;
        if(isset($_POST["leraktop"]) && count($player["hand"])==0 && $idx != ""){
            $lapertek = $player["top"][$idx];
        }else if(isset($_POST["lerakhidden"]) && count($player["top"])==0 && $idx != ""){
            $lapertek = $player["hidden"][$idx];
        }
        if($data["turn"]==$nid){
            if(canLerak($lapertek) && (isset($_POST["lerak"]) || isset($_POST["leraktop"]) || isset($_POST["lerakhidden"])) && $idx != ""){
                lerak();
                $lerakott = true;
            }else if((isset($_POST["felvesz"]) && canFelvesz()) || (!canLerak($lapertek) && isset($_POST["lerakhidden"]))  ){
                felvesz();
                $lerakott = true;
            }

            if($temeto==true){
                $data["table"]=[];
            }
            if($lerakott && !$temeto){
                nextturn();
            }
            if(count($data["players"][$nid]["hand"]) == 0 && count($data["players"][$nid]["top"]) == 0 && count($data["players"][$nid]["hidden"]) == 0 )
            {
                $winner = json_decode(file_get_contents('winners.json'), true);
                if(isset($winner[$id])){
                    $winner[$id]=$winner[$id]+1;
                }else{
                    $winner[$id]=1;
                }
                file_put_contents('winners.json', json_encode($winner, JSON_PRETTY_PRINT));
                $data=[];
                $data["running"]=false;
                $data["win"]=true;
                $data["winner"]=$id;
                $data["lastcard"]=$lastcard;
            }
            file_put_contents('gamestats.json', json_encode($data, JSON_PRETTY_PRINT));
        }
    }
    
    header("Location: cardselect.php");
?>