(() => {
 const button=document.querySelector('.menu-toggle'), nav=document.querySelector('.main-nav');
 if(!button || !nav) return;
 const setOpen=(open)=>{button.classList.toggle('active',open);nav.classList.toggle('active',open);button.setAttribute('aria-expanded',String(open));};
 button.addEventListener('click',()=>setOpen(button.getAttribute('aria-expanded')!=='true'));
 nav.querySelectorAll('a').forEach(link=>link.addEventListener('click',()=>setOpen(false)));
 document.addEventListener('keydown',event=>{if(event.key==='Escape')setOpen(false);});
 document.addEventListener('click',event=>{if(!nav.contains(event.target)&&!button.contains(event.target))setOpen(false);});
})();
