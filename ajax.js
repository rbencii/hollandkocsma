const disp = document.querySelector('#disp')
const disp2 = document.querySelector('#center')
const canvas = document.querySelector('canvas');
let ctx;

if(canvas){
    ctx = canvas.getContext('2d')
    ctx.font = "30px Arial";
}

imglist = [0,0];
for(let i = 2; i<15; i++){
    imglist.push("imgs/"+i+".png");
}



let img = [];
img.push(document.createElement('img'));
img[0].src="./imgs/hatter.png"
img.push(document.createElement('img'));
img[1].src="./imgs/0.png"
img.push(document.createElement('img'));
img[2].src="./imgs/sok.png"
img.push(document.createElement('img'));
for(let i=0;i<4*3;i++)
    img.push(document.createElement('img'));
img.push(document.createElement('img'));
img.push(document.createElement('img'));

const TO_RADIANS = Math.PI/180; 
function drawRotatedImage(image, x, y, angle, sx, sy)
{ 
    ctx.save(); 
    ctx.translate(x, y);
    ctx.rotate(angle * TO_RADIANS);
    ctx.drawImage(image, -(sx/2), -(sy/2), sx, sy);
    ctx.restore(); 
}

function rendergf(gf){
    const movey = 120;
    ctx.clearRect(0,0,1000,600)
    ctx.drawImage(img[0], 0, 0+movey, 1000, 600)
    let j=0;
    for(let i=0; i<gf.players[j].hidden; i++){
        ctx.drawImage(img[1],400-5+(i*100)-30,466+movey,72,114)
    }
    for(let i=0; i<gf.players[j].top.length; i++){
        img[3+i].src=imglist[gf.players[j].top[i]]
        ctx.drawImage(img[3+i],400-5+(i*100)-30,466-20+movey,72,114)
    }

    j=1;
    for(let i=0; i<gf.players[j].hidden; i++){
        drawRotatedImage(img[1],400-5+(i*100)-30+36,16+57+movey,180,72,114)
    }
    for(let i=0; i<gf.players[j].top.length; i++){
        img[6+i].src=imglist[gf.players[j].top[i]]
        ctx.drawImage(img[6+i],400-5+(i*100)-30,16+20+movey,72,114)
    }

    //1
    if(gf.players[1].hand>0){
        drawRotatedImage(img[2],500,25,180,206,150)
        ctx.fillText(gf.players[1].hand+"×", 570, 100)
        ctx.fillText(gf.players[1].name.slice(0,9), 620, 40)
        }
    
    if(gf.players.length==3 || gf.players.length==4){

    
    j=2;
    for(let i=0; i<gf.players[j].hidden; i++){
        drawRotatedImage(img[1],155+57,228+(i*100)-31+movey,90,72,114)
    }
    for(let i=0; i<gf.players[j].top.length; i++){
        img[9+i].src=imglist[gf.players[j].top[i]]
        drawRotatedImage(img[9+i],150+16+57+5,228+(i*100)-31+movey,90,72,114)
    }


  //2
  if(gf.players[2].hand>0){
    drawRotatedImage(img[2],25,400,90,206,150)
    ctx.fillText(gf.players[2].hand+"×", 60, 300)
    ctx.fillText(gf.players[2].name.slice(0,9), 10, 240)
    }

    }
    if(gf.players.length==4){

    j=3;
    for(let i=0; i<gf.players[j].hand; i++){
        drawRotatedImage(img[1],772+16,228+(i*100)-31+movey,-90,72,114)
    }
    for(let i=0; i<gf.players[j].top.length; i++){
        img[12+i].src=imglist[gf.players[j].top[i]]
        drawRotatedImage(img[12+i],772,228+(i*100)-31+movey,-90,72,114)
    }

    //3
    if(gf.players[3].hand>0){
        drawRotatedImage(img[2],975,400,-90,206,150)
        ctx.fillText(gf.players[3].hand+"×", 920, 300)
        ctx.fillText(gf.players[3].name.slice(0,9), 830, 240)
        }

}
    
    //table
    img[12].src=imglist[gf.table[gf.table.length-1]]
    ctx.drawImage(img[12],500-31,300-57+movey,72,114)

    const clamp = (num, min, max) => Math.min(Math.max(num, min), max);

    //deck
    for(let i=0; i<clamp(gf.cardcount,0,5); i++)
        ctx.drawImage(img[1],500-31-72-25+i,300-57-(i*2)+movey,72,114)

    
}

function gentable(arr){
    let pc =arr.players.length;
    disp.innerHTML=`<tr><th>id</th><th>nev</th><th>hand</th><th>top</th><th>hidden</th></tr>`
    for(let i=0; i<pc;i++){
        let tr = document.createElement('tr');
        for(key in arr.players[i]){
            let td = document.createElement('td');
            td.innerText=arr.players[i][key];
            tr.append(td)
        }
        disp.append(tr);
    }
    disp2.innerHTML=`<tr>
    <th>table</th>
    <th>
        turn
    </th>
    <th>
        cardcount
    </th>
</tr>
<tr><td>${arr.table}</td><td>${arr.turn}</td><td>${arr.cardcount}</td></tr>
`
}

async function turnUpdate(){
    let resp = await fetch('ajax.php');
    let data = await resp.json()
    if(data.running){
        gentable(data);
        rendergf(data);
        ctx.fillText(data.turnname+"'s turn.",10,30);
        if(data.turn == data.client && canvas){
            canvas.style.borderColor = "yellow";
        }else if(canvas){canvas.style.borderColor = 'rgb(108, 134, 204)'}
    }else if(data.win){
        if(canvas){
        ctx.clearRect(0,0,1000,712);
        ctx.fillText(data.winner+" won with the card: ",500-(data.winner+" won with the card: ").length*6,396);
        img[12].src=imglist[data.lastcard]
        ctx.drawImage(img[12],500-31,600-57-90,72,114)
        }
    }
}

setInterval(() => {
    turnUpdate();
}, 50);

