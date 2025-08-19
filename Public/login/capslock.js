// JavaScript Document
function  detectCapsLock(e){  
   valueCapsLock  =  e.keyCode ? e.keyCode:e.which; // Caps Lock　是否打开   
   valueShift  =  e.shiftKey ? e.shiftKey:((valueCapsLock  ==   16 ) ? true : false ); // shift键是否按住  

    if (((valueCapsLock >=   65   &&  valueCapsLock  <=   90 )  &&   ! valueShift) // Caps Lock 打开，并且 shift键没有按住   
    || ((valueCapsLock >=   97   &&  valueCapsLock  <=   122 )  &&  valueShift)) // Caps Lock 打开，并且按住 shift键   
       document.getElementById('capStatus').style.visibility  =  'visible';  
    else   
       document.getElementById('capStatus').style.visibility  =  'hidden';  

  /**//*   
 javascript中keyCode代码对应表  
 event.keyCode=32        空格  
 event.keyCode=13        回车  
 event.keyCode=27        Esc  
 event.keyCode=16)        Shift  
 event.keyCode=17)        Ctrl  
 event.keyCode=18)        Alt  
  */   

} 