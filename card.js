const lerak = document.querySelector('#lerak')
const felvesz = document.querySelector('#felvesz')
const count = document.querySelector("#count")
imglist = ["imgs/0.png",0];
for(let i = 2; i<15; i++){
    imglist.push("imgs/"+i+".png");
}

document.querySelectorAll("#gombok").forEach(x=>{
    let index = x.querySelector('input').id;
    x.style=`background-image: url("${imglist[index]}"); background-repeat: no-repeat; background-size: 59px 94px; padding: 38px 19px`
})

async function cardUpdate(){
    //console.log("a");
    let resp = await fetch('ajax.php');
    let data = await resp.json()
    if(data.running){
        if(data.client==data.turn){
            lerak.classList.remove("hidden");
            felvesz.classList.remove("hidden");
        }else{
            lerak.classList.add("hidden");
            felvesz.classList.add("hidden");
        }
    }
}

let selected = [];
let radio = "";

function handleSelect(p, obj){
    
    radio = obj.id;
    if(selected.includes(p) && selected.length>1){
        //console.log("as")
        selected=selected.filter(n=>n!=p);
    }else{
    selected.push(p);
    }
    selected=[...new Set(selected)];
    
    selected=selected.filter(n=>n.querySelector('input').id==radio)
}

document.querySelectorAll('#gombok').forEach(n=>n.addEventListener('click',function(e){
    if(e.target.id=="gombok"){
    let obj = e.target.querySelector('input');
    obj.checked=true;
    if(obj.id==0){
        document.querySelectorAll("#gombok").forEach(x=>x.classList.remove('selected'));
        e.target.classList.add('selected');
    }else{
    handleSelect(e.target, obj);
    document.querySelectorAll("#gombok").forEach(x=>x.classList.remove('selected'));
    selected.forEach(n=>n.classList.add('selected'));
    count.value=selected.length
    }
    }
}))
cardUpdate();
setInterval(() => {
    cardUpdate();
}, 100);