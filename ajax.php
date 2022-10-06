<?php
    session_start();
    $data = json_decode(file_get_contents('gamestats.json'), true);
    $resp = [];
    if(isset($_SESSION["id"])){
    $resp["running"]=$data["running"] && isset($_SESSION["id"]);
    if($resp["running"]){
        $id = $_SESSION["id"];
        $nid = $data["ids"][$id];
        $resp["turnname"]=$data["players"][$data["turn"]]["name"];
        $player = $data["players"][$nid];
        
        $resp["win"]=$data["win"] ?? false;
        $resp["client"]=$nid;
        $resp["turn"]=$data["turn"];
        $resp["table"]=$data["table"];
        $resp["cardcount"]=count($data["cards"]);
        $resp["players"][]=["id"=>$nid,
                            "name"=>$id,
                            "hand"=>$player["hand"],
                            "top"=>$player["top"],
                            "hidden"=>count($player["hidden"])];
        $pc=count($data["ids"]);
        for($i=0;$i<$pc;$i++){
            if($i!=$nid){
            $plr = $data["players"][$i];
            $resp["players"][]=["id"=>$i,
                                "name"=>$plr["name"],
                                "hand"=>count($plr["hand"]),
                                "top"=>$plr["top"],
                                "hidden"=>count($plr["hidden"])];
            }
        }
    }else{
        $resp["win"]=$data["win"] ?? false;
        $resp["winner"]=$data["winner"] ?? "";
        $resp["lastcard"]=$data["lastcard"] ?? 0;
    }
    }
    
    echo json_encode($resp);
?>