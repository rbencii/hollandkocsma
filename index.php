<?php
    $data = json_decode(file_get_contents('gamestats.json'), true);
    $winners = json_decode(file_get_contents('winners.json'), true);
    session_start();
    if(isset($_GET["logout"]) && isset($_SESSION["id"])){
        if($_GET["logout"]=="lepjki"){
            session_destroy();
            header("Location: index.php");
        }
        else if($_GET["logout"]=="end" && ($_SESSION["id"]=="Benci" || $_SESSION["id"]=="OrcY")){
            $data=[];
            $data["running"]=false;
            file_put_contents('gamestats.json', json_encode($data, JSON_PRETTY_PRINT));
            session_destroy();
            header("Location: index.php");
        }
    }

    if(isset($_POST["whichplayer"]) && !isset($_SESSION["id"])){
        $_SESSION["id"]=$_POST["whichplayer"];
        var_dump($_SESSION["id"]);
        header("Location: index.php");
    }

    if($data["running"]){
    
    }else{
        session_destroy();
        if(count($_POST)>1 && !isset($data["cards"])){
            $pc = $_POST["playercount"] ?? 0;
            if(intval($pc)>1){
            $p = [];
            $p[0] = $_POST["playerone"] ?? "Player1";
            $p[1] = $_POST["playertwo"] ?? "Player2";
            $p[2] = $_POST["playerthree"] ?? "Player3";
            $p[3] = $_POST["playerfour"] ?? "Player4";

            for($i=0;$i<intval($pc);$i++){
                $data["players"][$i] = ["name" => $p[$i]];
                $data["ids"][$p[$i]] = $i;
            }
            $cards=[];
            generatecards($cards);
            shuffle($cards);
            var_dump($data["players"]);
            dealcards($data["players"], $cards, intval($pc));
            $data["turn"]=0;
            $data["table"][]=array_pop($cards);
            $data["cards"]=$cards;
            $data["win"]=false;
            $data["running"]=true;
            file_put_contents('gamestats.json', json_encode($data, JSON_PRETTY_PRINT));
            header("Location: index.php");
            }
        }
    }

    function generatecards(&$cards){
        //global $cards;
        for($i=2;$i<15;$i++)
        {
            for($j=0;$j<4;$j++){
                $cards[]=$i;
            }
        }
    }

    function dealcards(&$players, &$cards, $pc){
        for($j=0;$j<$pc;$j++){
            for($i=0;$i<3;$i++){
                $players[$j]["hidden"][$i] = array_pop($cards);
            }
            for($i=0;$i<3;$i++){
                $players[$j]["top"][$i] = array_pop($cards);
            }
            for($i=0;$i<5;$i++){
                $players[$j]["hand"][$i] = array_pop($cards);
            }
        }
    }

    function Fisher_Yates_Shuffle(&$cards){
        //global $cards;
        for($i=52-1;$i>0;$i--){
            $j = random_int(0, $i);
            $tmp = $cards[$j];
            $cards[$j]=$cards[$i];
            $cards[$i]=$tmp;
        }
    }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="index.css">
    <title>Holland kocsma</title>
</head>
<body>
<?php if($data["running"] && isset($_SESSION["id"])): ?>
<div class="center">
    <div><canvas id="renderer" style="border:solid 1px #6c86cc; border-radius: 15px;" width="1000px" height="712px"> 
        
    </canvas></div>
    <table id="disp" class="hidden">
    </table>
    <table id="center" >
        <tr>
            <th>curr card</th>
            <th>
                cardcount
            </th>
        </tr>
    </table>
    
    <div>
    </div>
    <br>
    <div>
        <!-- HA KÉZBEN NINCS, TOP-RÓL!!!! -->
    <iframe src=cardselect.php width="502" height="162" style="border: 0px">

    </iframe>
    </div>
</div>
<?php elseif(!isset($data["cards"]) && isset($_GET["admin"])): ?>
<form action="index.php" method="POST">
    Playercount: <input type="number" name="playercount" id=""> <br>
    Player 1: <input type="text" name="playerone" id=""> <br>
    Player 2: <input type="text" name="playertwo" id=""> <br>
    Player 3: <input type="text" name="playerthree" id=""> <br>
    Player 4: <input type="text" name="playerfour" id=""> <br>
    <button type="submit" name="gen">Generate!</button>
</form>
<?php elseif(!isset($_SESSION["id"]) && isset($data["players"])): ?>
<form action="index.php" method="POST">
    <!--Melyik player: <input type="text" name="whichplayer" id=""> -->
    <?php foreach($data["players"] as $player): ?>
    <input type="radio" name="whichplayer" id="<?=$player["name"]?>" value="<?=$player["name"]?>">  <?=$player["name"]?>
    <br>
    <?php endforeach; ?>
    <button type="submit">Választ</button>
</form>
<?php else: ?>
    <h1 class="center">Várj amíg egy admin beállítja a játékot!</h1>
    <br>

    <div style="border: 1px solid black; border-radius: 10px; width:20%;margin-left: auto; margin-right: auto;">
    <table style=" margin-left: auto; margin-right: auto; text-align: center">
    <h2 class="center">Nyertesek:</h2>
        <tr>
            <th>Név</th>
            <th>Hányszor</th>
        </tr>
        <?php foreach($winners as $nev=>$cnt): ?>
        <tr>
            <td><?=$nev?></td>
            <td><?=$cnt?></td>
        </tr>
        <?php endforeach; ?>
    </table>
        </div>
<?php endif ?>




<form action="index.php" method="GET" style="position:absolute; top:95%; left:95%">
<button type="submit" name="logout" value="lepjki">Log out</button>
<button type="submit" name="logout" value="end">vege</button>
</form>
<script src="ajax.js"></script>
</body>
</html>
