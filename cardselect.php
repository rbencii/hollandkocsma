<?php
    $data = json_decode(file_get_contents('gamestats.json'), true);
    session_start();   
    if($data["running"]){
        $player=$data["players"][$data["ids"][$_SESSION["id"]]] ?? "";
        $spec="hand";
        $lerakspec="";
        if(count($player["hand"])>0){
            $spec = "hand";
            $lerakspec="";
        }else if(count($player["top"])>0){
            $spec = "top";
            $lerakspec="top";
        }else if(count($player["hidden"])>0){
            $spec = "hidden";
            $lerakspec="hidden";
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
<body style="margin:0">
<div class="select">
    <br><br>
<?php if(isset($_SESSION["id"]) && $data["running"]): ?>
    <form action="lerak.php" method="POST" class="center">
    <?php foreach($player[$spec] as $idx=>$value): ?>
        <span id="gombok"><input type="radio" style="opacity:0" name="valasztas" id="<?=($spec=="hidden")?"0":$value?>" value=<?=$idx?> <?=($idx==0)?"checked":""?>></span>
    <?php endforeach; ?>
    <br>
    <br>
    <br>
    <input type="number" name="count" id="count" class="hidden" value=0>
    <button id="lerak" class="" type="submit" name="lerak<?=$lerakspec?>">Lerak</button>  
    <button id="felvesz" class="" type="submit" name="felvesz">Felvesz</button>  
    </form>
<?php endif ?>
</div>
<script src="card.js"></script>
</body>
</html>